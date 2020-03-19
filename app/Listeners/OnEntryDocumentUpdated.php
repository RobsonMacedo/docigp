<?php

namespace App\Listeners;

use App\Events\CongressmanBudgetsChanged;
use App\Events\CongressmenChanged;
use App\Events\EntriesChanged;
use App\Events\EntryDocumentUpdated;
use App\Events\EntryDocumentsChanged;

class OnEntryDocumentUpdated extends Listener
{
    /**
     * Handle the event.
     *
     * @param  EntryDocumentUpdated  $event
     * @return void
     */
    public function handle(EntryDocumentUpdated $event)
    {
        event(new EntryDocumentsChanged($event->entryId));
        event(new EntriesChanged($event->congressmanBudgetId));
        event(new CongressmanBudgetsChanged($event->congressmanId));
        event(new CongressmenChanged());
    }
}
