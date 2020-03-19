<?php

namespace App\Data\Models;

use App\Data\Traits\Selectable;
use Illuminate\Support\Facades\DB;
use App\Data\Scopes\Published as PublishedScope;
use App\Data\Scopes\Congressman as CongressmanScope;

class Congressman extends Model
{
    use Selectable {
        getSelectColumnsRaw as protected getSelectColumnsRawOverloaded;
    }

    protected $fillable = [
        'remote_id',
        'name',
        'nickname',
        'party_id',
        'photo_url',
        'thumbnail_url',
        'department_id'
    ];

    protected $with = ['party', 'user'];

    protected $filterableColumns = ['name', 'nickname'];

    protected $orderBy = ['nickname' => 'asc'];

    protected $selectColumns = ['congressmen.*'];

    protected $appends = ['thumbnail_url_linkable', 'photo_url_linkable'];

    protected $selectColumnsRaw = [
        '(select count(*) from congressman_legislatures cl where cl.congressman_id = congressmen.id and cl.ended_at is null) > 0 as has_mandate',
        '(select count(*) from congressman_budgets cb join congressman_legislatures cl on cb.congressman_legislature_id = cl.id where cl.congressman_id = congressmen.id and cb.published_at is null) > 0 as has_pendency',
        '(select count(*) from congressman_budgets cb join congressman_legislatures cl on cb.congressman_legislature_id = cl.id join entries e on e.congressman_budget_id = cb.id where cl.congressman_id = congressmen.id and cb.published_at is not null and e.published_at is not null) > 0 as is_published'
    ];

    public function legislatures()
    {
        return $this->hasMany(CongressmanLegislature::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function congressmanBudgets()
    {
        return $this->hasManyThrough(
            CongressmanBudget::class,
            CongressmanLegislature::class
        );
    }

    protected function joinCongressmanLegislatures($query)
    {
        return $query->join(
            DB::raw(
                '(select cgltemp.id as cgl_id, cgltemp.ended_at, cgltemp.congressman_id from congressman_legislatures as cgltemp) as cgl'
            ),
            'cgl.congressman_id',
            '=',
            'congressmen.id'
        );
    }

    public function scopeActive($query)
    {
        return $this->joinCongressmanLegislatures($query)->whereNull(
            'cgl.ended_at'
        );
    }

    public function scopeNonActive($query)
    {
        return $this->joinCongressmanLegislatures($query)
            ->whereNotNull('cgl.ended_at')
            ->whereNotExists(function ($query) {
                $query
                    ->select(DB::raw(1))
                    ->from('congressman_legislatures')
                    ->whereRaw(
                        'congressman_legislatures.congressman_id = congressmen.id'
                    )
                    ->whereNull('congressman_legislatures.ended_at');
            });
    }

    public function scopeWithPendency($query)
    {
        return $query->whereRaw(
            '(select count(*) from congressman_budgets cb join congressman_legislatures cl on cb.congressman_legislature_id = cl.id where cl.congressman_id = congressmen.id and cb.published_at is null) > 0'
        );
    }

    public function scopeWithoutPendency($query)
    {
        return $query->whereRaw(
            '(select count(*) from congressman_budgets cb join congressman_legislatures cl on cb.congressman_legislature_id = cl.id where cl.congressman_id = congressmen.id and cb.published_at is null) = 0'
        );
    }

    public function scopeUnread($query)
    {
        if ($select = $this->buildUnreadQuery()) {
            $query->whereRaw("($select) > 0");
        }

        return $query;
    }

    public function scopeJoined($query)
    {
        if ($select = $this->buildJoinedQuery()) {
            $query->whereRaw("($select) > 0");
        }

        return $query;
    }

    public function scopeNotJoined($query)
    {
        if ($select = $this->buildJoinedQuery()) {
            $query->whereRaw("($select) = 0");
        }

        return $query;
    }

    /**
     * @return \App\Data\Models\CongressmanBudget|null
     */
    public function getCurrentBudgetAttribute()
    {
        return $this->congressmanBudgets()
            ->join(
                'budgets',
                'budgets.id',
                '=',
                'congressman_budgets.budget_id'
            )
            ->orderBy('budgets.date', 'desc')
            ->first();
    }

    /**
     * @param $congressmanBudget
     * @return \App\Data\Models\CongressmanBudget|null
     */
    public function getPriorBudgetRelativeTo($congressmanBudget)
    {
        return $this->congressmanBudgets()
            ->orderBy('budgets.date', 'desc')
            ->join(
                'budgets',
                'budgets.id',
                '=',
                'congressman_budgets.budget_id'
            )
            ->where(
                'budgets.date',
                '<',
                $congressmanBudget->budget->date->startOfMonth()
            )
            ->first();
    }

    /**
     * @param $congressmanBudget
     * @return \App\Data\Models\CongressmanBudget|null
     */
    public function getNextBudgetRelativeTo($congressmanBudget)
    {
        return $this->congressmanBudgets()
            ->orderBy('budgets.date', 'asc')
            ->join(
                'budgets',
                'budgets.id',
                '=',
                'congressman_budgets.budget_id'
            )
            ->where(
                'budgets.date',
                '>',
                $congressmanBudget->budget->date->endOfMonth()
            )
            ->first();
    }

    /**
     * @return \App\Data\Models\Legislature|null
     */
    public function getCurrentLegislatureAttribute()
    {
        return $this->legislatures()
            ->whereNull('ended_at')
            ->first();
    }

    public function file()
    {
        return $this->morphOne(AttachedFile::class, 'fileable');
    }

    public function party()
    {
        return $this->belongsTo(Party::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope(new CongressmanScope());
    }

    public static function disableGlobalScopes()
    {
        PublishedScope::disable();
        CongressmanScope::disable();
    }

    public static function enableGlobalScopes()
    {
        PublishedScope::enable();
        CongressmanScope::enable();
    }

    public function getThumbnailUrlLinkableAttribute()
    {
        return filled($this->thumbnail_url)
            ? 'http://' . trim($this->thumbnail_url)
            : null;
    }

    public function getPhotoUrlLinkableAttribute()
    {
        return filled($this->photo_url)
            ? 'http://' . trim($this->photo_url)
            : null;
    }

    public function getSelectColumnsRaw()
    {
        $selectColumns = $this->getSelectColumnsRawOverloaded();

        if ($select = $this->buildUnreadSelect()) {
            $selectColumns[] = $select;
        }

        return $selectColumns;
    }

    public function buildUnreadSelect()
    {
        $query = $this->buildUnreadQuery();

        return $query ? "($query) > 0 as unread" : '';
    }

    public function buildUnreadQuery()
    {
        $userId = auth()->user()->id ?? false;

        return $userId
            ? "select
                     count(*)
                   from congressmen c2
                       join changes_unread on c2.id = changes_unread.congressman_id
                   where c2.id = congressmen.id
                     and changes_unread.user_id = {$userId}
                    "
            : '';
    }

    public function buildJoinedQuery()
    {
        return "select 
                count(*) 
               from congressman_budgets cb 
               join congressman_legislatures cl on cb.congressman_legislature_id = cl.id 
               join entries e on e.congressman_budget_id = cb.id 
               where cl.congressman_id = congressmen.id 
               and cb.published_at is not null 
               and e.published_at is not null
                    ";
    }
}
