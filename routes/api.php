<?php

use Illuminate\Http\Request;

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

Route::get('/', function (Request $request) {
    return 'Working';
});

// Registration, confirmations and verification
Route::post('password/email', 'Auth\ForgotPasswordController@getResetToken');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');
Route::post('auth/register', 'Auth\AuthController@register');
Route::get('auth/verify', 'Auth\AuthController@verify');


// Authentication Routes
Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('login', 'Auth\AuthController@login');
    Route::post('change-password', 'Auth\AuthController@changePassword');
    Route::post('logout', 'Auth\AuthController@logout');
    Route::post('refresh', 'Auth\AuthController@refresh');
    Route::post('me', 'Auth\AuthController@me');
});

// Superadmin Routes
Route::group(['middleware' => ['jwt.verify','role:superadmin'],'prefix' => 'sadmin','namespace'=>'Admin'], function($router)
{	
  
    // User Routes
    Route::post('user','UserAndRoleController@createUser');
    Route::post('user/update', 'UserAndRoleController@updateUser');
    Route::get('users', 'UserAndRoleController@getUsers');
    Route::post('users/dt', 'UserAndRoleController@getDTUsers');
    Route::get('user/{id}', 'UserAndRoleController@getUser');    
    Route::delete('user/{id}/delete', 'UserAndRoleController@deleteUser');
    Route::get('user/{id}/reset-password', 'UserAndRoleController@resetPassword');

    Route::post('user/role/assign', 'UserAndRoleController@assignRole');
    Route::get('user/role/{id}', 'UserAndRoleController@getUserRole');

    // Role Routes
    Route::post('role', 'UserAndRoleController@createRole');
    Route::post('role/update', 'UserAndRoleController@updateRole');
    Route::get('roles', 'UserAndRoleController@getRoles');
    Route::post('roles/dt', 'UserAndRoleController@getDTRoles');
    Route::get('role/{id}', 'UserAndRoleController@getRole');
    Route::delete('role/{id}/delete', 'UserAndRoleController@deleteRole');
    
});



