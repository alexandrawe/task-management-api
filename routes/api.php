<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectTasksController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserTasksController;
use App\Http\Middleware\AuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware(AuthMiddleware::class)
    ->whereNumber('id')
    ->controller(TaskController::class)
    ->group(function () {
        Route::get('/tasks', 'index');
        Route::get('/tasks/overdue', 'overdue');
        Route::get('/tasks/{id}', 'show');
        Route::post('/tasks', 'store');
        Route::patch('/tasks/{id}', 'update');
        Route::delete('/tasks/{id}', 'destroy');
});

Route::get('/user/{id}/tasks', UserTasksController::class)
    ->whereNumber('id')
    ->middleware(AuthMiddleware::class);

Route::get('/project/{id}/tasks', ProjectTasksController::class)
    ->whereNumber('id')
    ->middleware(AuthMiddleware::class);