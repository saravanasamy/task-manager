<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

Route::get('/', function () {
    return redirect()->route('tasks.index');
});

Route::resource('tasks', TaskController::class);

// Additional task routes
Route::patch('/tasks/{task}/complete', [TaskController::class, 'markCompleted'])->name('tasks.complete');
Route::post('/tasks/bulk-action', [TaskController::class, 'bulkAction'])->name('tasks.bulk-action');
