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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('task', 'TasksController@save')->name('tasks.save');
Route::get('tasks/gantt', 'TasksController@getGanttTasks')->name('tasks.get.gantt');

Route::get('tasks/{id?}', 'TasksController@get')->name('tasks.get');

Route::get('pipelines', 'PipelinesController@get')->name('pipelines.get');

Route::get('users', 'UsersController@get')->name('users.get');
