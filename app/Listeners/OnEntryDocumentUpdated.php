<?php

namespace App\Listeners;

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
        event(new EntryDocumentsChanged($event->entryDocumentId));
    }
}
