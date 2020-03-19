<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\EntryAnalyse;
use App\Http\Requests\EntryDelete;
use App\Http\Requests\EntryPublish;
use App\Http\Requests\EntryStore;
use App\Http\Requests\EntryUpdate;
use App\Http\Controllers\Controller;
use App\Data\Repositories\Entries as EntriesRepository;
use App\Http\Requests\EntryVerify;

class Entries extends Controller
{
    /**
     * Get all data
     *
     * @param $congressmanId
     * @param $congressmanBudgetId
     * @return array
     */
    public function all($congressmanId, $congressmanBudgetId)
    {
        return app(EntriesRepository::class)->allFor(
            $congressmanId,
            $congressmanBudgetId
        );
    }

    public function verify(
        EntryVerify $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        app(EntriesRepository::class)->verify($entryId);
    }

    public function unverify(
        EntryVerify $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        app(EntriesRepository::class)->unverify($entryId);
    }

    public function analyse(
        EntryAnalyse $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        app(EntriesRepository::class)->analyse($entryId);
    }

    public function unanalyse(
        EntryAnalyse $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        app(EntriesRepository::class)->unanalyse($entryId);
    }

    public function publish(
        EntryPublish $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        app(EntriesRepository::class)->publish($entryId);
    }

    public function unpublish(
        EntryPublish $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        app(EntriesRepository::class)->unpublish($entryId);
    }

    public function delete(
        EntryDelete $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        app(EntriesRepository::class)->delete($entryId);
    }

    public function emptyRefundForm($congressmanId, $congressmanBudgetId)
    {
        return app(EntriesRepository::class)->emptyRefundForm(
            $congressmanBudgetId
        );
    }

    /**
     * Store
     *
     * @param EntryStore $request
     * @param $congressmanId
     * @param $congressmanBudgetId
     * @return mixed
     */
    public function store(
        EntryStore $request,
        $congressmanId,
        $congressmanBudgetId
    ) {
        return app(EntriesRepository::class)
            ->setCongressmanBudgetId($congressmanBudgetId)
            ->setData($request->all())
            ->store();
    }

    /**
     * @param EntryUpdate $request
     * @param $id
     * @return mixed
     */
    public function update(
        EntryUpdate $request,
        $congressmanId,
        $congressmanBudgetId,
        $entryId
    ) {
        return app(EntriesRepository::class)
            ->setCongressmanBudgetId($congressmanBudgetId)
            ->setData($request->all())
            ->update($entryId);
    }
}
