<?php

namespace App\Http\Requests;

use App\Data\Models\CongressmanBudget;
use App\Http\Traits\WithRouteParams;
use Illuminate\Support\Facades\Gate;

class CongressmanBudgetUpdate extends Request
{
    use WithRouteParams;

    /**
     * @return bool
     */
    public function authorize()
    {
        $congressmanBudget = CongressmanBudget::find(
            $this->all()['congressmanBudgetId']
        );

        return $congressmanBudget &&
            Gate::allows(
                'congressman-budgets:update:model',
                $congressmanBudget
            ) &&
            (allows('congressman-budgets:update') ||
                allows('congressman-budgets:percentage'));
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
