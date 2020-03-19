<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use App\Http\Traits\WithRouteParams;
use App\Data\Models\Entry;

class EntryChangeState extends Request
{
    use WithRouteParams;

    public $action;
    public $ableFunction;

    /**
     * @return bool
     */
    public function authorize()
    {
        $entry = Entry::find($this->all()['entryId']);

        return $entry &&
            $entry->{$this->ableFunction}() &&
            Gate::allows('entries:update:model', $entry) &&
            allows('entries:' . $this->action);
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
