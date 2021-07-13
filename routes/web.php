<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/', function () {
    return view('welcome');
})->middleware('auth');
// Route::post('temporal', 'App\Http\Controllers\v1\AuthController@cambioContrasenaGenerica'); /////

Auth::routes(['register' => false]);
Route::post('auth/setContext', 'v1\AuthController@setContext')->middleware('auth');
Route::get('formatos/estimacion/{id}/orden-pago', 'v1\CADECO\Contratos\EstimacionController@pdfOrdenPago')->where(['id' => '[0-9]+']);
//Route::get('finanzas/distribuir-recurso-remesa/{id}/layout', 'v1\CADECO\Finanzas\DistribucionRecursoRemesaController@descargaLayout')->where(['id' => '[0-9]+']);
Route::get('finanzas/distribuir-recurso-remesa/{id}/layoutManual', 'v1\CADECO\Finanzas\DistribucionRecursoRemesaController@descargaLayoutManual')->where(['id' => '[0-9]+']);

Route::get('{any}', function () {
    return view('welcome');
})
    ->middleware('auth')
    ->where('any', '.*');
