<?php

namespace App\Http\Requests;

class CongressmanBudgetPublish extends CongressmanBudgetChangeState
{
    public $action = 'publish';
    public $ableFunction = 'isPublishable';
}
