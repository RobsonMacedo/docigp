<?php

namespace App\Listeners;

use App\Events\EntriesChanged;
use App\Events\EntryUpdated;

class OnEntryUpdated extends Listener
{
    /**
     * Handle the event.
     *
     * @param  EntryUpdated  $event
     * @return void
     */
    public function handle(EntryUpdated $event)
    {
        event(new EntriesChanged($event->entryId));
    }
}
