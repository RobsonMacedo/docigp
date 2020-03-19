<?php

namespace App\Http\Requests;

class EntryDocumentAnalyse extends EntryDocumentChangeState
{
    public $action = 'analyse';
    public $ableFunction = 'isAnalysable';
}
