<?php

namespace App\Http\Requests;

class CongressmanBudgetClose extends CongressmanBudgetChangeState
{
    public $action = 'close';
    public $ableFunction = 'isClosable';
}
