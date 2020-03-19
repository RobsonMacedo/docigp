<?php

namespace App\Data\Models;

use App\Support\Constants;
use App\Data\Scopes\Published;
use App\Data\Traits\MarkAsUnread;
use App\Data\Repositories\Budgets;
use App\Data\Traits\ModelActionable;
use App\Data\Repositories\CostCenters;
use App\Data\Repositories\CongressmanBudgets;
use App\Data\Scopes\Congressman as CongressmanScope;

class CongressmanBudget extends Model
{
    use ModelActionable, MarkAsUnread;

    /**
     * @var array
     */
    protected $fillable = [
        'congressman_legislature_id',
        'budget_id',
        'percentage',
        'analysed_by_id',
        'analysed_at',
        'published_by_id',
        'published_at',
        'closed_by_id',
        'closed_at'
    ];

    protected $appends = ['has_refund'];

    protected $with = [
        'budget',
        'congressmanLegislature',
        'congressmanLegislature.congressman'
    ];

    protected $selectColumns = ['congressman_budgets.*'];

    protected $selectColumnsRaw = [
        '(select count(*) from entries e where e.congressman_budget_id = congressman_budgets.id and e.analysed_at is null) > 0 as missing_analysis',
        '(select count(*) from entries e where e.congressman_budget_id = congressman_budgets.id and e.verified_at is null) > 0 as missing_verification',
        '(select count(*) from entries e where e.congressman_budget_id = congressman_budgets.id and e.entry_type_id = ' .
            Constants::ENTRY_TYPE_ALERJ_DEPOSIT_ID .
            ') > 0 as has_deposit',
        '(select count(*) from entries e where e.congressman_budget_id = congressman_budgets.id :published-at-filter:) as entries_count',
        '(select sum(value) from entries e where e.congressman_budget_id = congressman_budgets.id and value > 0) as sum_credit',
        '(select sum(value) from entries e where e.congressman_budget_id = congressman_budgets.id and value < 0) as sum_debit'
    ];

    protected $orderBy = ['budgets.date' => 'desc'];

    protected $joins = [
        'budgets' => ['budgets.id', '=', 'congressman_budgets.budget_id']
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new Published());

        static::saved(function (CongressmanBudget $model) {
            $model->markAsUnread();
        });

        static::created(function (CongressmanBudget $model) {
            $model->updateTransportEntries();
        });
    }

    private function createTransportEntry($balance, $date, $type)
    {
        $isCredit = $type == 'credit';

        $this->{'transport_' .
            $type .
            '_entry_id'} = ($entry2 = Entry::updateOrCreate(
            [
                'congressman_budget_id' => $this->id,
                'cost_center_id' => app(CostCenters::class)->findByCode(
                    $isCredit
                        ? Constants::COST_CENTER_TRANSPORT_CREDIT_ID
                        : Constants::COST_CENTER_TRANSPORT_DEBIT_ID
                )->id
            ],
            [
                'to' => $this->congressman->name,
                'provider_id' => Constants::ALERJ_PROVIDER_ID,
                'entry_type_id' => Constants::ENTRY_TYPE_TRANSPORT_ID,
                'object' =>
                    'Transporte de saldo ' .
                    ($isCredit
                        ? 'do período anterior'
                        : 'para o próximo período'),
                'date' => $isCredit
                    ? $date->startOfMonth()
                    : $date->endOfMonth(),
                'value' => $balance
            ]
        ))->id;

        $this->save();
    }

    /**
     * @return mixed
     */
    private function makeEmptyTransport($costCenterId)
    {
        return Entry::where('congressman_budget_id', $this->id)
            ->where('cost_center_id', $costCenterId)
            ->get()
            ->each(function (Entry $entry) {
                $entry->value = 0;
                $entry->save();
            });
    }

    protected function fillValue(): bool
    {
        if ($this->percentageChanged()) {
            $budget = app(Budgets::class)->findById($this->budget_id);

            $this->value = ($budget->value * $this->percentage) / 100;

            if (
                $budget->date->year >= config('app.year_round_change') &&
                $budget->date->month >= config('app.month_round_change')
            ) {
                $this->value = trunc_value_with_two_digits($this->value);
            }
            return true;
        }

        return false;
    }

    /**
     * @param $balance\
     */
    private function updateTransportEntry($balance, $type)
    {
        Entry::disableEvents();

        $this->createTransportEntry($balance, $this->budget->date, $type);

        Entry::enableEvents();
    }

    protected function percentageChanged()
    {
        return blank($this->value) ||
            ($this->isDirty('percentage') && !$this->isDirty('value'));
    }

    /**
     * Save the model to the database.
     *
     * @param  array  $options
     * @return bool
     */
    public function save(array $options = [])
    {
        $updated = $this->fillValue();

        $saved = parent::save();

        if ($updated) {
            $this->updateDepositEntry();

            app(CongressmanBudgets::class)->updateAllEntriesFor($this->id);
        }

        return $saved;
    }

    public function entries()
    {
        return $this->hasMany(Entry::class);
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function getBalance()
    {
        return $this->entries()
            ->selectRaw('sum(value) as balance')
            ->first()->balance ?? 0;
    }

    public function getBalanceWithoutDebitTransport()
    {
        return $this->entries()
            ->selectRaw('sum(value) as balance')
            ->whereNotIn('cost_center_id', [
                Constants::COST_CENTER_TRANSPORT_DEBIT_ID
            ]) // débito
            ->first()->balance ?? 0;
    }

    public function congressmanLegislature()
    {
        return $this->belongsTo(CongressmanLegislature::class);
    }

    public function congressman()
    {
        return $this->congressmanLegislature->congressman();
    }

    public function deposit()
    {
        if ($this->has_deposit) {
            return;
        }

        Entry::create([
            'congressman_budget_id' => $this->id,
            'to' => $this->congressman->name,
            'provider_id' => Constants::ALERJ_PROVIDER_ID,
            'object' => 'Crédito em conta-corrente',
            'cost_center_id' => Constants::COST_CENTER_CREDIT_ID,
            'entry_type_id' => Constants::ENTRY_TYPE_ALERJ_DEPOSIT_ID,
            'date' => $this->budget->date->startOfMonth() ?? now(),
            'value' => $this->value
        ]);
    }

    public function isDepositable()
    {
        return !$this->has_deposit && blank($this->closed_at);
    }

    public function isClosable()
    {
        return blank($this->analysed_at);
    }

    public function isAnalysable()
    {
        return $this->closed_at && blank($this->published_at);
    }

    public function updateTransportEntries()
    {
        //$next é o mês seguinte ao atual
        if ($next = $this->congressman->getNextBudgetRelativeTo($this)) {
            //Valor de todos os lançamentos do mês sem o transporte
            $value = $this->getBalanceWithoutDebitTransport();

            //Aqui é criado o transporte de crédito para o próximo mês
            $next->updateTransportEntry(
                $balance = abs($value = $value < 0 ? 0 : $value),
                'credit'
            );

            //$balance tem o valor que vai ser transportado para o próximo mês. Se for negativo, fica zero
            //Aqui é criado o transporte de débito no mês atual
            $this->updateTransportEntry($balance * -1, 'debit');

            $next->updateTransportEntries();
        }
    }

    public static function disableGlobalScopes()
    {
        Published::disable();

        CongressmanScope::disable();
    }

    public static function enableGlobalScopes()
    {
        Published::disable();

        CongressmanScope::enable();
    }

    public function updateDepositEntry()
    {
        $deposit = $this->entries
            ->where('cost_center_id', Constants::COST_CENTER_CREDIT_ID)
            ->first();

        if ($deposit && $this->value) {
            $deposit->value = $this->value;
            $deposit->save();
        }
    }

    public function getHasRefundAttribute()
    {
        $found = false;
        $this->entries->each(function (Entry $entry) use (&$found) {
            $found = $found || $entry->costCenter->code == 4;
        });

        return $found;
    }
}
