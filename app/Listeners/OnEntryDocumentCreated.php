<?php

namespace App\Listeners;

use App\Events\CongressmanBudgetsChanged;
use App\Events\CongressmenChanged;
use App\Events\EntriesChanged;
use App\Events\EntryDocumentsChanged;
use App\Events\EntryDocumentCreated;

class OnEntryDocumentCreated extends Listener
{
    /**
     * Handle the event.
     *
     * @param  EntryDocumentCreated  $event
     * @return void
     */
    public function handle(EntryDocumentCreated $event)
    {
        event(new EntryDocumentsChanged($event->entryId));
        event(new EntriesChanged($event->congressmanBudgetId));
        event(new CongressmanBudgetsChanged($event->congressmanId));
        event(new CongressmenChanged());
    }
}
