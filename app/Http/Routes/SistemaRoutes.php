<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\SistemaController as Controller;

Route::prefix('sistemas')->middleware('auth:api')->group(function () {
    Route::get('', Controller::class . '@index');
    Route::get('/{id}', Controller::class . '@show');
    Route::delete('/{id}', Controller::class . '@delete');
    Route::post('', Controller::class . '@create');
    Route::patch('/{id}', Controller::class . '@update');
});
