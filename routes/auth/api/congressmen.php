<?php

Route::group(['prefix' => '/congressmen'], function () {
    Route::post('/{id}', 'Congressmen@update')->name('congressmen.update');

    Route::post('/', 'Congressmen@store')->name('congressmen.store');

    Route::group(['prefix' => '/{congressmanId}/budgets'], function () {
        Route::group(['prefix' => '/{congressmanBudgetId}'], function () {
            Route::post('/', 'CongressmanBudgets@update')->name(
                'congressmen.budgets.update'
            );

            Route::post('/close', 'CongressmanBudgets@close')->name(
                'congressmen.budgets.close'
            );

            Route::post('/reopen', 'CongressmanBudgets@reopen')->name(
                'congressmen.budgets.reopen'
            );

            Route::post('/analyse', 'CongressmanBudgets@analyse')->name(
                'congressmen.budgets.analyse'
            );

            Route::post('/unanalyse', 'CongressmanBudgets@unanalyse')->name(
                'congressmen.budgets.unanalyse'
            );

            Route::post('/publish', 'CongressmanBudgets@publish')->name(
                'congressmen.budgets.publish'
            );

            Route::post('/unpublish', 'CongressmanBudgets@unpublish')->name(
                'congressmen.budgets.unpublish'
            );

            Route::post('/deposit', 'CongressmanBudgets@deposit')->name(
                'congressmen.budgets.deposit'
            );
        });

        Route::post('/', 'CongressmanBudgets@store')->name(
            'congressmen.budgets.store'
        );

        Route::group(
            ['prefix' => '/{congressmanBudgetId}/entries'],
            function () {
                Route::post('/', 'Entries@store')->name('congressmen.store');

                Route::get(
                    '/empty-refund-form',
                    'Entries@emptyRefundForm'
                )->name('congressmen.empty-refund-form');

                Route::group(['prefix' => '/{entryId}'], function () {
                    Route::post('/', 'Entries@update')->name(
                        'congressmen.update'
                    );

                    Route::post('/delete', 'Entries@delete')->name(
                        'congressmen.budgets.entries.delete'
                    );

                    Route::post('/verify', 'Entries@verify')->name(
                        'congressmen.budgets.entries.verify'
                    );

                    Route::post('/unverify', 'Entries@unverify')->name(
                        'congressmen.budgets.entries.unverify'
                    );

                    Route::post('/analyse', 'Entries@analyse')->name(
                        'congressmen.budgets.entries.analyse'
                    );

                    Route::post('/unanalyse', 'Entries@unanalyse')->name(
                        'congressmen.budgets.entries.unanalyse'
                    );

                    Route::post('/publish', 'Entries@publish')->name(
                        'congressmen.budgets.entries.publish'
                    );

                    Route::post('/unpublish', 'Entries@unpublish')->name(
                        'congressmen.budgets.entries.unpublish'
                    );

                    Route::group(['prefix' => '/documents'], function () {
                        Route::post('/', 'EntryDocuments@store')->name(
                            'congressmen.budgets.entries-documents.store'
                        );

                        Route::group(
                            ['prefix' => '/{documentId}'],
                            function () {
                                Route::post(
                                    '/publish',
                                    'EntryDocuments@publish'
                                )->name(
                                    'congressmen.budgets.entries-documents.publish'
                                );

                                Route::post(
                                    '/unpublish',
                                    'EntryDocuments@unpublish'
                                )->name(
                                    'congressmen.budgets.entries-documents.unverify'
                                );

                                Route::post(
                                    '/verify',
                                    'EntryDocuments@verify'
                                )->name(
                                    'congressmen.budgets.entries-documents.verify'
                                );

                                Route::post(
                                    '/unverify',
                                    'EntryDocuments@unverify'
                                )->name(
                                    'congressmen.budgets.entries-documents.unverify'
                                );

                                Route::post(
                                    '/analyse',
                                    'EntryDocuments@analyse'
                                )->name(
                                    'congressmen.budgets.entries-documents.analyse'
                                );

                                Route::post(
                                    '/unanalyse',
                                    'EntryDocuments@unanalyse'
                                )->name(
                                    'congressmen.budgets.entries-documents.unanalyse'
                                );

                                Route::post(
                                    '/delete',
                                    'EntryDocuments@delete'
                                )->name(
                                    'congressmen.budgets.entries-documents.delete'
                                );
                            }
                        );
                    });

                    Route::group(['prefix' => '/comments'], function () {
                        Route::post('/', 'EntryComments@store')->name(
                            'congressmen.budgets.entries-comments.store'
                        );

                        Route::group(['prefix' => '/{commentId}'], function () {

                            Route::post('/', 'EntryComments@update')->name(
                                'congressmen.budgets.entries-comments.update'
                            );

                            Route::post(
                                '/delete',
                                'EntryComments@delete'
                            )->name(
                                'congressmen.budgets.entries-comments.delete'
                            );
                        });
                    });
                });
            }
        );
    });
});
