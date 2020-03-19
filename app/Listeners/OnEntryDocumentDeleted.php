<?php

namespace App\Listeners;

use App\Events\CongressmanBudgetsChanged;
use App\Events\CongressmenChanged;
use App\Events\EntriesChanged;
use App\Events\EntryDocumentDeleted;
use App\Events\EntryDocumentsChanged;

class OnEntryDocumentDeleted extends Listener
{
    /**
     * Handle the event.
     *
     * @param  EntryDocumentDeleted  $event
     * @return void
     */
    public function handle(EntryDocumentDeleted $event)
    {
        event(new EntryDocumentsChanged($event->entryId));
        event(new EntriesChanged($event->congressmanBudgetId));
        event(new CongressmanBudgetsChanged($event->congressmanId));
        event(new CongressmenChanged());
    }
}
