<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/task', [TaskController::class, 'store'])->name('task.create');
Route::get('/task', [TaskController::class, 'index'])->name('task.index');
Route::get('/task/{task}', [TaskController::class, 'show'])->name('task.show');
Route::get('/task/{task}/hosts/completed', [TaskController::class, 'completed'])->name('task.hosts.completed');

