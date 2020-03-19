<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CongressmanStore;
use App\Http\Requests\CongressmanBudgetUpdate;
use App\Data\Repositories\CongressmanBudgets as CongressmanBudgetsRepository;
use App\Http\Requests\CongressmanBudgetClose;
use App\Http\Requests\CongressmanBudgetReopen;
use App\Http\Requests\CongressmanBudgetPublish;
use App\Http\Requests\CongressmanBudgetAnalyse;
use App\Http\Requests\CongressmanBudgetDeposit;

class CongressmanBudgets extends Controller
{
    /**
     * Get all data
     *
     * @param $congressmanId
     * @return array
     */
    public function all($congressmanId)
    {
        return app(CongressmanBudgetsRepository::class)->allFor($congressmanId);
    }

    /**
     * Store
     *
     * @param CongressmanStore $request
     * @return mixed
     */
    public function store(CongressmanStore $request)
    {
        return app(CongressmanBudgetsRepository::class)->storeFromArray(
            $request->all()
        );
    }

    /**
     * @param CongressmanBudgetUpdate $request
     * @param $id
     * @return mixed
     */
    public function update(
        CongressmanBudgetUpdate $request,
        $congressmanId,
        $budgetId
    ) {
        return app(CongressmanBudgetsRepository::class)->update(
            $budgetId,
            $request->all()
        );
    }

    /**
     * Get all data
     *
     * @return array
     */
    public function availableCongressmanBudgets()
    {
        return app(
            CongressmanBudgetsRepository::class
        )->getAvailableCongressmanBudgets();
    }

    public function close(
        CongressmanBudgetClose $request,
        $congressmanId,
        $congressmanBudgetId
    ) {
        app(CongressmanBudgetsRepository::class)->close($congressmanBudgetId);
    }

    public function reopen(
        CongressmanBudgetReopen $request,
        $congressmanId,
        $congressmanBudgetId
    ) {
        app(CongressmanBudgetsRepository::class)->reopen($congressmanBudgetId);
    }

    public function analyse(
        CongressmanBudgetAnalyse $request,
        $congressmanId,
        $congressmanBudgetId
    ) {
        app(CongressmanBudgetsRepository::class)->analyse($congressmanBudgetId);
    }

    public function unanalyse(
        CongressmanBudgetAnalyse $request,
        $congressmanId,
        $congressmanBudgetId
    ) {
        app(CongressmanBudgetsRepository::class)->unanalyse(
            $congressmanBudgetId
        );
    }

    public function publish(
        CongressmanBudgetPublish $request,
        $congressmanId,
        $congressmanBudgetId
    ) {
        app(CongressmanBudgetsRepository::class)->publish($congressmanBudgetId);
    }

    public function unpublish(
        CongressmanBudgetPublish $request,
        $congressmanId,
        $congressmanBudgetId
    ) {
        app(CongressmanBudgetsRepository::class)->unpublish(
            $congressmanBudgetId
        );
    }

    public function deposit(
        CongressmanBudgetDeposit $request,
        $congressmanId,
        $congressmanBudgetId
    ) {
        app(CongressmanBudgetsRepository::class)->deposit($congressmanBudgetId);
    }
}
