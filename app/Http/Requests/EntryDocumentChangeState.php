<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use App\Http\Traits\WithRouteParams;
use App\Data\Models\EntryDocument;

class EntryDocumentChangeState extends Request
{
    use WithRouteParams;

    public $action;
    public $ableFunction;

    /**
     * @return bool
     */
    public function authorize()
    {
        $entryDocument = EntryDocument::find($this->all()['documentId']);

        return $entryDocument &&
            $entryDocument->{$this->ableFunction}() &&
            Gate::allows('entry-documents:update:model', $entryDocument) &&
            allows('entry-documents:' . $this->action);
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
