<?php

namespace App\Http\Requests;

class EntryPublish extends EntryChangeState
{
    public $action = 'publish';
    public $ableFunction = 'isPublishable';
}
