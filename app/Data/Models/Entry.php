<?php

namespace App\Data\Models;

use App\Data\Scopes\Published;
use App\Data\Traits\MarkAsUnread;
use App\Data\Traits\ModelActionable;
use App\Data\Scopes\Published as PublishedScope;

class Entry extends Model
{
    use ModelActionable, MarkAsUnread;

    protected $table = 'entries';

    /**
     * @var array
     */
    protected $fillable = [
        'date',
        'value',
        'object',
        'to',
        'provider_id',
        'cost_center_id',
        'congressman_budget_id',
        'entry_type_id',
        'document_number',
        'verified_at',
        'analysed_at',
        'published_at',
        'verified_by_id',
        'analysed_by_id',
        'published_by_id',
        'created_by_id',
        'updated_by_id'
    ];

    protected $dates = ['date', 'verified_at', 'analysed_at', 'published_at'];

    protected $selectColumns = [
        'entries.*',
        'cost_centers.name as cost_center_name',
        'cost_centers.code as cost_center_code',
        'providers.name as provider_name',
        'providers.cpf_cnpj as provider_cpf_cnpj',
        'providers.type as provider_type',
        'entry_types.name as entry_type_name'
    ];

    protected $selectColumnsRaw = [
        '(select count(*) from entry_documents ed where ed.entry_id = entries.id :published-at-filter: :analysed-at-filter:) as documents_count',
        '(select count(*) from entry_documents ed where ed.entry_id = entries.id and ed.verified_at is null :published-at-filter:) > 0 as missing_verification',
        '(select count(*) from entry_documents ed where ed.entry_id = entries.id and ed.analysed_at is null :published-at-filter:) > 0 as missing_analysis'
    ];

    protected $filterableColumns = [
        'entries.to',
        'entries.object',
        'entries.value'
    ];

    protected $orderBy = ['date' => 'desc'];

    protected $joins = [
        'providers' => ['providers.id', '=', 'entries.provider_id', 'left'],
        'cost_centers' => ['cost_centers.id', '=', 'entries.cost_center_id'],
        'entry_types' => ['entry_types.id', '=', 'entries.entry_type_id']
    ];

    protected $updatingTransport = false;

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new Published());

        static::saved(function (Entry $model) {
            if (static::$modelEventsEnabled) {
                $model->updateTransport();
            }

            $model->markAsUnread();
        });

        static::deleted(function (Entry $model) {
            if (static::$modelEventsEnabled) {
                $model->updateTransport();
            }
        });
    }

    public function documents()
    {
        return $this->hasMany(EntryDocument::class);
    }

    public function congressmanBudget()
    {
        return $this->belongsTo(CongressmanBudget::class);
    }

    public function file()
    {
        return $this->morphOne(AttachedFile::class, 'fileable');
    }

    public function provider()
    {
        return $this->belongsTo(Provider::class);
    }

    public function costCenter()
    {
        return $this->belongsTo(CostCenter::class);
    }

    public function updateTransport()
    {
        if ($this->updatingTransport) {
            return;
        }

        $this->updatingTransport = true;

        $this->congressmanBudget->updateTransportEntries();

        $this->updatingTransport = false;
    }

    public static function disableGlobalScopes()
    {
        PublishedScope::disable();
    }

    public static function enableGlobalScopes()
    {
        PublishedScope::disable();
    }

    public function congressman()
    {
        return $this->congressmanBudget->congressman();
    }

    public function isVerifiable()
    {
        return blank($this->congressmanBudget->closed_at);
    }
}
