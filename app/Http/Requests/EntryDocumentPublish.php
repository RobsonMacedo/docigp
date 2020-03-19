<?php

namespace App\Http\Requests;

class EntryDocumentPublish extends EntryDocumentChangeState
{
    public $action = 'publish';
    public $ableFunction = 'isPublishable';
}
