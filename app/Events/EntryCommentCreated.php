<?php

namespace App\Events;

use App\Data\Models\Entry;

class EntryCommentCreated extends Event
{
    public $entryCommentId;
    public $entryId;
    public $congressmanBudgetId;

    /**
     * Create a new entry Comment instance.
     *
     * @param $entryComment
     */
    public function __construct($entryComment)
    {
        $this->entryCommentId = $entryComment->id;
        $this->entryId = $entryComment->entry_id;

        $this->congressmanBudgetId = Entry::find($entryComment->entry_id)->congressman_budget_id;
    }
}
