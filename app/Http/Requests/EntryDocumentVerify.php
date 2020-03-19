<?php

namespace App\Http\Requests;

class EntryDocumentVerify extends EntryDocumentChangeState
{
    public $action = 'verify';
    public $ableFunction = 'isVerifiable';
}
