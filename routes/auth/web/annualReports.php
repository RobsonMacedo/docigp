<?php
Route::group(['prefix' => '/annualReports'], function () {
    Route::post('/', 'AnnualReports@index')->name('annualReports.index');
});

?>