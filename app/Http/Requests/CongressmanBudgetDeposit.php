<?php

namespace App\Http\Requests;

class CongressmanBudgetDeposit extends CongressmanBudgetChangeState
{
    public $action = 'deposit';
    public $ableFunction = 'isDepositable';
}
