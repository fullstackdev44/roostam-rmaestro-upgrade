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

Route::get('/', 'HomeController@index');

Auth::routes();

Route::get('/home', 'HomeController@index');
Route::group(['prefix' => 'api', 'namespace' => 'Api', 'middleware' => ['web', 'auth']], function () {
   
    Route::resource('tasks', 'APIController');
    
    Route::get('task-create', 'APIController@createAndStoreTask');
    Route::get('tasks/copy-task/{id}', 'APIController@copyAndStoreTask');
    Route::get('tasks/get-root-task/{id}', 'APIController@getRootTask');
    
    Route::get('tasks-action-view', 'APIController@actionLists');
    Route::get('tasks-tasks-view', 'APIController@index');
    Route::get('tasks-projects-view', 'APIController@projectLists');
    Route::get('tasks-templates-view', 'APIController@templateLists');

    Route::get('task-children/{id}', 'APIController@getChildren');
    
    Route::get('tasks/execute-task/{id}', 'APIController@executeTask');
    Route::get('task-done/{id}', 'APIController@taskDone');

    Route::get('get-desks/{id}', 'APIController@getDesks');
    Route::post('desk-window-update/{id}', 'APIController@updateWindow');
    Route::post('desk-window-new', 'APIController@newWindow');
    Route::get('desk-window-delete/{id}', 'APIController@deleteWindow');

});