<?php

namespace App\Http\Requests;

class EntryAnalyse extends EntryChangeState
{
    public $action = 'analyse';
    public $ableFunction = 'isAnalysable';
}
