<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TaskController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('api')->group(function () {
    
    // Task Management API Routes
    Route::prefix('tasks')->name('api.tasks.')->group(function () {
        
        // Specific endpoints first (before parameterized routes)
        Route::get('/statistics/overview', [TaskController::class, 'statistics'])->name('statistics');
        Route::get('/filter/overdue', [TaskController::class, 'overdue'])->name('overdue');
        Route::get('/search/query', [TaskController::class, 'search'])->name('search');
        Route::get('/export/data', [TaskController::class, 'export'])->name('export');
        Route::post('/bulk-action', [TaskController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/status/{status}', [TaskController::class, 'byStatus'])->name('by-status');
        
        // Standard CRUD operations (parameterized routes last)
        Route::get('/', [TaskController::class, 'index'])->name('index');
        Route::post('/', [TaskController::class, 'store'])->name('store');
        Route::get('/{id}', [TaskController::class, 'show'])->name('show');
        Route::put('/{id}', [TaskController::class, 'update'])->name('update');
        Route::patch('/{id}', [TaskController::class, 'update'])->name('patch');
        Route::delete('/{id}', [TaskController::class, 'destroy'])->name('destroy');
        Route::patch('/{id}/complete', [TaskController::class, 'markCompleted'])->name('complete');
        
    });
    
    // API Health Check
    Route::get('/health', function () {
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'API is healthy',
            'data' => [
                'service' => 'Task Manager API',
                'version' => '1.0.0',
                'timestamp' => now()->toISOString(),
                'environment' => app()->environment()
            ],
            'validation_errors' => null,
            'timestamp' => now()->toISOString()
        ]);
    })->name('api.health');
    
    // API Documentation endpoint
    Route::get('/docs', function () {
        return response()->json([
            'status' => true,
            'status_code' => 200,
            'message' => 'API Documentation',
            'data' => [
                'endpoints' => [
                    'tasks' => [
                        'GET /api/tasks' => 'Get all tasks with pagination and filters',
                        'POST /api/tasks' => 'Create a new task',
                        'GET /api/tasks/{id}' => 'Get specific task',
                        'PUT /api/tasks/{id}' => 'Update specific task',
                        'DELETE /api/tasks/{id}' => 'Delete specific task',
                        'GET /api/tasks/statistics/overview' => 'Get task statistics',
                        'GET /api/tasks/status/{status}' => 'Get tasks by status',
                        'GET /api/tasks/filter/overdue' => 'Get overdue tasks',
                        'GET /api/tasks/search/query' => 'Search tasks',
                        'GET /api/tasks/export/data' => 'Export tasks data',
                        'PATCH /api/tasks/{id}/complete' => 'Mark task as completed',
                        'POST /api/tasks/bulk-action' => 'Perform bulk operations'
                    ]
                ],
                'authentication' => 'No authentication required for this demo',
                'base_url' => url('/api'),
                'version' => '1.0.0'
            ],
            'validation_errors' => null,
            'timestamp' => now()->toISOString()
        ]);
    })->name('api.docs');
    
});
