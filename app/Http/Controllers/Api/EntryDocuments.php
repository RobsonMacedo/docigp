<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntryDocumentAnalyse;
use App\Http\Requests\EntryDocumentDelete;
use App\Http\Requests\EntryDocumentPublish;
use App\Http\Requests\EntryDocumentStore;
use App\Data\Repositories\EntryDocuments as EntryDocumentsRepository;
use App\Http\Requests\EntryDocumentVerify;

class EntryDocuments extends Controller
{
    /**
     * Get all data
     *
     * @param $congressmanId
     * @param $congressmanBudgetId
     * @return array
     */
    public function all($congressmanId, $congressmanBudgetId, $entryId)
    {
        return app(EntryDocumentsRepository::class)->allFor(
            $congressmanId,
            $congressmanBudgetId,
            $entryId
        );
    }

    /**
     * Store
     *
     * @param \App\Http\Requests\EntryDocumentStore $request
     * @param $congressmanId
     * @param $congressmanBudgetId
     * @param $entryId
     * @return mixed
     */
    public function store(
        EntryDocumentStore $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        return app(EntryDocumentsRepository::class)
            ->setEntryId($entryId)
            ->setData($request->all())
            ->store();
    }

    public function publish(
        EntryDocumentPublish $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId,
        $entryDocumentId
    ) {
        app(EntryDocumentsRepository::class)->publish($entryDocumentId);
    }

    public function unpublish(
        EntryDocumentPublish $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId,
        $entryDocumentId
    ) {
        app(EntryDocumentsRepository::class)->unpublish($entryDocumentId);
    }

    public function verify(
        EntryDocumentVerify $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId,
        $entryDocumentId
    ) {
        app(EntryDocumentsRepository::class)->verify($entryDocumentId);
    }

    public function unverify(
        EntryDocumentVerify $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId,
        $entryDocumentId
    ) {
        app(EntryDocumentsRepository::class)->unverify($entryDocumentId);
    }

    public function analyse(
        EntryDocumentAnalyse $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId,
        $entryDocumentId
    ) {
        app(EntryDocumentsRepository::class)->analyse($entryDocumentId);
    }

    public function unanalyse(
        EntryDocumentAnalyse $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId,
        $entryDocumentId
    ) {
        app(EntryDocumentsRepository::class)->unanalyse($entryDocumentId);
    }

    public function delete(
        EntryDocumentDelete $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId,
        $entryDocumentId
    ) {
        app(EntryDocumentsRepository::class)->delete($entryDocumentId);
    }
}
