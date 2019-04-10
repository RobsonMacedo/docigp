<?php

use App\Data\Repositories\EntryTypes;
use App\Data\Repositories\Providers;
use App\Data\Repositories\CostCenters;
use App\Data\Models\Entry as EntryModel;
use App\Data\Repositories\CongressmanBudgets;
use App\Data\Repositories\Users as UsersRepository;

$factory->define(EntryModel::class, function () {
    return [
        'date' => faker()->dateTimeBetween(
            $startDate = \Carbon\Carbon::now()->startOfMonth(),
            $endDate = \Carbon\Carbon::now()->endOfMonth(),
            $timezone = null
        ),
        'value' => faker()->randomFloat(2, 0.1, 1000),
        'object' => faker()->text(30),
        'to' => faker()->name,

        'entry_type_id' => ($entry_type = app(
            EntryTypes::class
        )->randomElement())->id,

        'document_number' => $entry_type->document_number_required
            ? faker()->numberBetween(1, 9999)
            : null,

        'provider_id' =>
            rand(0, 10) == 0
                ? null
                : app(Providers::class)->randomElement([1])->id,

        'cost_center_id' => app(CostCenters::class)->randomElement([1])->id,

        'congressman_budget_id' => app(
            CongressmanBudgets::class
        )->randomElement()->id,
        'verified_at' => ($verified = rand(0, 1) ? faker()->date : null),
        'analysed_at' => ($analysed =
            $verified && rand(0, 1) ? faker()->date : null),
        'published_at' => ($published =
            $analysed && rand(0, 1) ? faker()->date : null),
        'verified_by_id' => $verified
            ? app(UsersRepository::class)->randomElement()->id
            : null,
        'analysed_by_id' => $analysed
            ? app(UsersRepository::class)->randomElement()->id
            : null,
        'published_by_id' => $published
            ? app(UsersRepository::class)->randomElement()->id
            : null,
        'created_by_id' => app(UsersRepository::class)->randomElement()->id,
        'updated_by_id' => app(UsersRepository::class)->randomElement()->id,
    ];
});
