<?php

namespace App\Http\Requests;

class CongressmanBudgetReopen extends CongressmanBudgetChangeState
{
    public $action = 'reopen';
    public $ableFunction = 'isClosable';
}
