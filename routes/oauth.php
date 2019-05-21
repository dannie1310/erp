<?php

Route::group(['middleware' => ['web', 'auth']], function () {
    Route::get('/authorize', [
        'uses' => 'Auth\Passport\AuthorizationController@authorize',
        'as' => 'passport.authorizations.authorize',
    ]);

    Route::post('/authorize', [
        'uses' => 'Auth\Passport\ApproveAuthorizationController@approve',
        'as' => 'passport.authorizations.approve',
    ]);

    Route::delete('/authorize', [
        'uses' => 'Auth\Passport\DenyAuthorizationController@deny',
        'as' => 'passport.authorizations.deny',
    ]);
});