<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\EntryCommentStore;
use App\Data\Repositories\EntryComments as EntryCommentsRepository;

class EntryComments extends Controller
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
        return app(EntryCommentsRepository::class)->allFor(
            $congressmanId,
            $congressmanBudgetId,
            $entryId
        );
    }

    /**
     * Store
     *
     * @param \App\Http\Requests\EntryCommentStore $request
     * @param $congressmanId
     * @param $congressmanBudgetId
     * @param $entryId
     * @return mixed
     */
    public function store(
        EntryCommentStore $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        return app(EntryCommentsRepository::class)
            ->setEntryId($entryId)
            ->setData($request->all())
            ->store();
    }

    public function update(
        EntryCommentStore $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId,
        $entryCommentId
    ) {
        return app(EntryCommentsRepository::class)
            ->setEntryId($entryId)
            ->setData($request->all())
            ->update($entryCommentId);
    }


    public function delete(
        $congressmanId,
        $congressmanBudgetId,
        $entryId,
        $entryCommentId
    ) {
        app(EntryCommentsRepository::class)->delete($entryCommentId);
    }
}
