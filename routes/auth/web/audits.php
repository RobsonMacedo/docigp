<?php

Route::group(['prefix' => '/audits'], function () {
    Route::get('/', 'Audits@index')->name('audits.index');
    Route::get('/export', 'Users@export')->name('audits.export');
});
