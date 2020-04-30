<?php

Route::group(
    [
        'prefix' => '/admin',
        'namespace' => 'Web\Admin',
        'middleware' => ['auth'] //, 'app.users'],
    ],
    function () {
        Route::get('/phpinfo', function () {
            phpinfo();
            die();
        });

        require __DIR__ . '/web/home.php';
        require __DIR__ . '/web/legislatures.php';
        require __DIR__ . '/web/parties.php';
        require __DIR__ . '/web/congressmen.php';
        require __DIR__ . '/web/congressmanLegislatures.php';
        require __DIR__ . '/web/entries.php';
        require __DIR__ . '/web/users.php';
        require __DIR__ . '/web/providers.php';
        require __DIR__ . '/web/costCenters.php';
        require __DIR__ . '/web/entryTypes.php';
    }
);
