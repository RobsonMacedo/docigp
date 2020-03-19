<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Gate;
use App\Http\Traits\WithRouteParams;
use App\Data\Models\EntryDocument;

class EntryDocumentDelete extends Request
{
    use WithRouteParams;

    /**
     * @return bool
     */
    public function authorize()
    {
        $entryDocument = EntryDocument::find($this->all()['documentId']);

        return $entryDocument &&
            Gate::allows('entry-documents:update:model', $entryDocument) &&
            allows('entry-documents:delete');
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
