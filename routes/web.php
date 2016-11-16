<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Auth::routes();

Route::get('/', 'HomeController@index');

Route::get('/dashboard', 'DashboardController@index');

Route::get('/profile', 'UserController@profile');

/*
|--------------------------------------------------------------------------
| Users - manage the users of the site
|--------------------------------------------------------------------------
|
*/

Route::get('/users', 'UsersController@index');

/*
|--------------------------------------------------------------------------
| Locations - manage the locations of units
|--------------------------------------------------------------------------
|
*/

Route::resource('locations', 'LocationsController');

/*
|--------------------------------------------------------------------------
| Units - manage the units
|--------------------------------------------------------------------------
|
*/

Route::resource('units', 'UnitsController');

/*
|--------------------------------------------------------------------------
| Unit types - manage the unit types
|--------------------------------------------------------------------------
|
*/

Route::resource('unit-types', 'UnitTypesController');

/*
|--------------------------------------------------------------------------
| Items - manage the items a unit type might have
|--------------------------------------------------------------------------
|
*/

Route::resource('items', 'ItemsController');

/*
|--------------------------------------------------------------------------
| Contracts - manage the contracts
|--------------------------------------------------------------------------
|
*/

Route::resource('contracts', 'ContractsController');

/*
|--------------------------------------------------------------------------
| Application form - manage the users application form
|--------------------------------------------------------------------------
|
*/

Route::get('/application-form', 'ApplicationController@create');
Route::post('/application-form', 'ApplicationController@store');
Route::get('/application-form/{id}/edit', 'ApplicationController@edit');
Route::post('/step-1/{id}', 'ApplicationController@stepOne');
Route::post('/step-2/{id}', 'ApplicationController@stepTwo');
Route::post('/step-3/{id}', 'ApplicationController@stepThree');
Route::post('/step-4/{id}', 'ApplicationController@stepFour');
Route::post('/step-5/{id}', 'ApplicationController@stepFive');
Route::post('/step-6/{id}', 'ApplicationController@stepSix');
Route::post('/step-7/{id}', 'ApplicationController@stepSeven');
Route::post('/step-8/{id}', 'ApplicationController@stepEight');
Route::get('/application-form/{id}', 'ApplicationController@show');