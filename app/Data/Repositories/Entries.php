<?php

namespace App\Data\Repositories;

use App\Support\Constants;
use Carbon\Carbon;
use App\Data\Models\Entry;
use Illuminate\Support\Str;
use App\Data\Traits\RepositoryActionable;

class Entries extends Repository
{
    use RepositoryActionable;

    /**
     * @var string
     */
    protected $model = Entry::class;

    protected $congressmanBudgetId;

    protected $data;

    public function allFor($congressmanId, $congressmanBudgetId)
    {
        return $this->applyFilter(
            $this->newQuery()
                ->join(
                    'congressman_budgets',
                    'congressman_budgets.id',
                    'entries.congressman_budget_id'
                )
                ->join(
                    'congressman_legislatures',
                    'congressman_legislatures.id',
                    'congressman_budgets.congressman_legislature_id'
                )
                ->where(
                    'congressman_legislatures.congressman_id',
                    $congressmanId
                )
                ->where('congressman_budgets.id', $congressmanBudgetId)
        );
    }

    private function firstOrCreateProvider($to, $provider_cpf_cnpj)
    {
        return app(Providers::class)->firstOrCreate(
            ['cpf_cnpj' => $provider_cpf_cnpj],
            ['name' => $to]
        );
    }


    /**
     * @param mixed $congressmanBudgetId
     * @return \App\Data\Repositories\Entries
     */
    public function setCongressmanBudgetId($congressmanBudgetId): Entries
    {
        $this->congressmanBudgetId = $congressmanBudgetId;

        return $this;
    }

    public function transform($data)
    {
        
        $this->addTransformationPlugin(function ($entry) {
            $entry['date_formatted'] = Carbon::parse($entry['date'])->format(
                'd/m/Y'
            );

            $entry['date'] = $entry['date_formatted'];

            $entry['value_formatted'] = to_reais($entry['value']);

            $entry['value_abs'] = abs($entry['value']);

            $entry['cost_center_name_formatted'] = Str::limit(
                $entry['cost_center_name'],
                60,
                '...'
            );

            $entry['name'] = ($forCongressman = in_array(
                $entry['cost_center_id'],
                Constants::COST_CENTER_CONTROL_ID_ARRAY
            ))
                ? $entry['to']
                : $entry['provider_name'];

            $entry['cpf_cnpj'] = $forCongressman
                ? null
                : "{$entry['provider_type']}: {$entry['provider_cpf_cnpj']}";

            $entry['pendencies'] = $this->buildPendenciesArray($entry);
            
            return $entry;
        });

        return parent::transform($data);
    }

    private function buildPendenciesArray($entry)
    {
        $pendencies = [];

        if ($entry['missing_verification']) {
            $pendencies[] = 'verificar documentos';
        }

        if (blank($entry['verified_at'])) {
            $pendencies[] = 'verificar lançamento';
        }

        if ($entry['missing_analysis']) {
            $pendencies[] = 'analisar documentos';
        }

        if (blank($entry['analysed_at'])) {
            $pendencies[] = 'analisar lançamento';
        }

        if (
            blank($entry['published_at']) &&
            !$entry['is_transport_or_credit']
        ) {
            $pendencies[] = 'publicar';
        }

        return $pendencies;
    }

    public function store()
    {
        $this->data['congressman_budget_id'] = $this->congressmanBudgetId;

        $this->data['provider_id'] = $this->firstOrCreateProvider(
            $this->data['to'],
            $this->data['provider_cpf_cnpj']
        )->id;

        $this->storeFromArray($this->data);
    }

    /**
     * @param $id
     * @param $array
     * @return mixed
     */
    public function update($id, $array = null)
    {
        $this->data['provider_id'] = $this->firstOrCreateProvider(
            $this->data['provider_name'],
            $this->data['provider_cpf_cnpj']
        )->id;

        $this->fillAndSave($array ?? $this->data, $this->findById($id));
        return parent::update($id, $array);
    }

    /**
     * @param $callable
     * @return mixed
     */
    public function withGlobalScopesDisabled($callable)
    {
        Entry::disableGlobalScopes();

        $result = $callable();

        Entry::enableGlobalScopes();

        return $result;
    }

    public function emptyRefundForm($congressmanBudgetId)
    {
        $congressmanBudget = app(CongressmanBudgets::class)->findById(
            $congressmanBudgetId
        );

        $date = $congressmanBudget->budget->date;

        $date->day =
            now()->month == $date->month
                ? now()->day
                : $date->endOfMonth()->day;

        $provider = app(Providers::class)->getAlerj();

        $form = [
            'congressman_budget_id' => (int) $congressmanBudgetId,
            'cost_center_id' => app(CostCenters::class)->findByCode(
                Constants::COST_CENTER_REFUND_CODE
            )->id,
            'provider_cpf_cnpj' => $provider->cpf_cnpj,
            'date' => $date->format('d/m/Y'),
            'document_number' => null,
            'entry_type_id' => app(EntryTypes::class)->getRefundEntryType()->id,
            'object' => 'Devolução de verba orçamentária',
            'provider_id' => $provider->id,
            'provider_name' => $provider->name,
            'value' => 0,
            'value_abs' => 0
        ];

        return $form;
    }
}
