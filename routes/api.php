<?php

use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\RegisterController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('company/register', [CompanyController::class, 'registerCompany']);
Route::post('company/login', [CompanyController::class, 'loginCompany']);

Route::group([
    'middleware' => 'auth:api',
    'prefix'     => 'company'
], function () {
    Route::post('/register-package', [CompanyController::class, 'registerPackage']);
    Route::post('/information', [CompanyController::class, 'getCompanyInfo']);
});

