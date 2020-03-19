<?php

namespace App\Events;

use App\Data\Models\Entry;

class EntryCommentDeleted extends Event
{
    public $entryCommentId;
    public $entryId;
    public $congressmanBudgetId;

    /**
     * Create a new entry Comment instance.
     *
     * @param $entry
     */
    public function __construct($entryComment)
    {
        $entryComment = (object) $entryComment->toArray();

        $this->entryCommentId = $entryComment->id;

        $entry = Entry::find($entryComment->entry_id);
        $this->entryId = $entry->id;

        $this->congressmanBudgetId = $entry->congressman_budget_id;
    }
}
