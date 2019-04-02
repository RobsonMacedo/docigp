<?php

use App\Data\Repositories\Budgets;
use App\Services\DataSync\Service as DataSyncService;

Artisan::command('docigp:sync:congressmen', function () {
    app(DataSyncService::class)->congressmen();
})->describe('Sync congressmen data');

Artisan::command('docigp:sync:parties', function () {
    app(DataSyncService::class)->parties();
})->describe('Sync congressmen data');

Artisan::command('docigp:budget:generate', function () {
    app(Budgets::class)->generate();
})->describe('Sync congressmen data');
