<?php

namespace App\Data\Traits;

trait Selectable
{
    public function getSelectColumns()
    {
        return coollect(
            isset($this->selectColumns) ? $this->selectColumns : []
        );
    }

    public function getSelectColumnsRaw()
    {
        return $this->replaceWheres(
            coollect(
                isset($this->selectColumnsRaw) ? $this->selectColumnsRaw : []
            )
        );
    }

    public function replaceWheres($selects)
    {
        return $selects->map(function ($select) {
            return $this->replaceWhere($select);
        });
    }

    public function replaceWhere($select)
    {
        $select = str_replace(
            ':published-at-filter:',
            !auth()->user() ? 'and published_at is not null' : '',
            $select
        );

        $select = str_replace(
            ':analysed-at-filter:',
            !auth()->user() ? 'and analysed_at is not null' : '',
            $select
        );

        return $select;
    }
}
