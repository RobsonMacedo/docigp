<?php

namespace App\Http\Requests;

class EntryVerify extends EntryChangeState
{
    public $action = 'verify';
    public $ableFunction = 'isVerifiable';
}
