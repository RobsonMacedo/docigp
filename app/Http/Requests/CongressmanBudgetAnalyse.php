<?php

namespace App\Http\Requests;

class CongressmanBudgetAnalyse extends CongressmanBudgetChangeState
{
    public $action = 'analyse';
    public $ableFunction = 'isAnalysable';
}
