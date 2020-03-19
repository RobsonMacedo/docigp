<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use App\Http\Traits\WithRouteParams;
use App\Data\Models\Entry;

class EntryDelete extends Request
{
    use WithRouteParams;

    /**
     * @return bool
     */
    public function authorize()
    {
        $entry = Entry::find($this->all()['entryId']);

        return $entry &&
            Gate::allows('entries:update:model', $entry) &&
            allows('entries:delete');
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
