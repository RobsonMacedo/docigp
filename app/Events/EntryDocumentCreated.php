<?php

namespace App\Events;

use App\Data\Models\CongressmanBudget;

class EntryDocumentCreated extends Event
{
    public $entryDocumentId;
    public $entryId;
    public $congressmanBudgetId;
    public $congressmanId;

    /**
     * Create a new entry document instance.
     *
     * @param $entryDocument
     */
    public function __construct($entryDocument)
    {
        $this->entryDocumentId = $entryDocument->id;
        $this->entryId = $entryDocument->entry_id;
        $entry = $entryDocument->entry;
        $this->congressmanBudgetId = $entry->congressman_budget_id;
        $this->congressmanId = CongressmanBudget::find(
            $this->congressmanBudgetId
        )->congressman->id;
    }
}
