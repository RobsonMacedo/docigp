<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use App\Http\Traits\WithRouteParams;
use App\Data\Models\CongressmanBudget;

class CongressmanBudgetChangeState extends Request
{
    use WithRouteParams;

    public $action;
    public $ableFunction;

    /**
     * @return bool
     */
    public function authorize()
    {
        $congressmanBudget = CongressmanBudget::find(
            $this->all()['congressmanBudgetId']
        );

        return $congressmanBudget &&
            $congressmanBudget->{$this->ableFunction}() &&
            Gate::allows(
                'congressman-budgets:update:model',
                $congressmanBudget
            ) &&
            allows('congressman-budgets:' . $this->action);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [];
    }
}
