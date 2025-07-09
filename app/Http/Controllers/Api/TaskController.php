<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use App\Services\ApiResponseService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of tasks with filters and pagination
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $tasks = $this->taskService->getAllTasks($request);
            $statistics = $this->taskService->getTaskStatistics();

            return ApiResponseService::paginated(
                $tasks,
                'Tasks retrieved successfully'
            )->withHeaders([
                'X-Total-Count' => $tasks->total(),
                'X-Statistics' => json_encode($statistics)
            ]);

        } catch (ValidationException $e) {
            return ApiResponseService::validationError($e, 'Invalid filter parameters');
        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Store a newly created task
     */
    public function store(StoreTaskRequest $request): JsonResponse
    {
        try {
            $task = $this->taskService->createTask($request->validated());

            return ApiResponseService::created(
                $task->load([]), // Add relationships if needed
                'Task created successfully'
            );

        } catch (ValidationException $e) {
            return ApiResponseService::validationError($e, 'Task creation failed due to validation errors');
        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Display the specified task
     */
    public function show(int $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);

            return ApiResponseService::success(
                $task,
                'Task retrieved successfully'
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Task not found');
        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Update the specified task
     */
    public function update(UpdateTaskRequest $request, int $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $updatedTask = $this->taskService->updateTask($task, $request->validated());

            return ApiResponseService::success(
                $updatedTask,
                'Task updated successfully'
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Task not found');
        } catch (ValidationException $e) {
            return ApiResponseService::validationError($e, 'Task update failed due to validation errors');
        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Remove the specified task
     */
    public function destroy(int $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $this->taskService->deleteTask($task);

            return ApiResponseService::success(
                ['deleted_task_id' => $id],
                'Task deleted successfully'
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Task not found');
        } catch (ValidationException $e) {
            return ApiResponseService::validationError($e, 'Task deletion failed due to business rules');
        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Get task statistics
     */
    public function statistics(): JsonResponse
    {
        try {
            $statistics = $this->taskService->getTaskStatistics();

            return ApiResponseService::success(
                $statistics,
                'Task statistics retrieved successfully'
            );

        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Get tasks by status
     */
    public function byStatus(string $status): JsonResponse
    {
        try {
            // Validate status
            $this->taskService->getValidationService()->validateStatusChange($status);
            
            $tasks = $this->taskService->getTasksByStatus($status);

            return ApiResponseService::collection(
                $tasks,
                "Tasks with status '{$status}' retrieved successfully"
            );

        } catch (ValidationException $e) {
            return ApiResponseService::validationError($e, 'Invalid status provided');
        } catch (\InvalidArgumentException $e) {
            return ApiResponseService::error($e->getMessage(), 400);
        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Get overdue tasks
     */
    public function overdue(): JsonResponse
    {
        try {
            $overdueTasks = $this->taskService->getOverdueTasks();

            return ApiResponseService::collection(
                $overdueTasks,
                'Overdue tasks retrieved successfully'
            );

        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Mark task as completed
     */
    public function markCompleted(int $id): JsonResponse
    {
        try {
            $task = Task::findOrFail($id);
            $completedTask = $this->taskService->markAsCompleted($task);

            return ApiResponseService::success(
                $completedTask,
                'Task marked as completed successfully'
            );

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return ApiResponseService::notFound('Task not found');
        } catch (ValidationException $e) {
            return ApiResponseService::validationError($e, 'Cannot mark task as completed');
        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Bulk operations on tasks
     */
    public function bulkAction(Request $request): JsonResponse
    {
        try {
            $result = $this->taskService->processBulkOperation($request->all());

            return ApiResponseService::bulkOperation(
                $result,
                $result['message']
            );

        } catch (ValidationException $e) {
            return ApiResponseService::validationError($e, 'Bulk operation failed due to validation errors');
        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Search tasks
     */
    public function search(Request $request): JsonResponse
    {
        try {
            // Validate search parameters
            $validated = $request->validate([
                'query' => 'required|string|min:1|max:255',
                'fields' => 'nullable|array|in:title,description',
                'per_page' => 'nullable|integer|min:1|max:100'
            ]);

            $query = Task::query();
            
            // Apply search
            $searchTerm = $validated['query'];
            $fields = $validated['fields'] ?? ['title', 'description'];
            
            $query->where(function ($q) use ($searchTerm, $fields) {
                foreach ($fields as $field) {
                    $q->orWhere($field, 'like', "%{$searchTerm}%");
                }
            });

            $perPage = $validated['per_page'] ?? 10;
            $tasks = $query->paginate($perPage);

            return ApiResponseService::paginated(
                $tasks,
                "Search results for '{$searchTerm}'"
            );

        } catch (ValidationException $e) {
            return ApiResponseService::validationError($e, 'Invalid search parameters');
        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }

    /**
     * Export tasks (returns data for export)
     */
    public function export(Request $request): JsonResponse
    {
        try {
            // Get all tasks without pagination for export
            $tasks = Task::query();
            
            // Apply filters if provided
            if ($request->filled('status')) {
                $tasks->where('status', $request->status);
            }
            
            if ($request->filled('start_date')) {
                $tasks->where('due_date', '>=', $request->start_date);
            }
            
            if ($request->filled('end_date')) {
                $tasks->where('due_date', '<=', $request->end_date);
            }

            $exportData = $tasks->get()->map(function ($task) {
                return [
                    'id' => $task->id,
                    'title' => $task->title,
                    'description' => $task->description,
                    'status' => $task->status,
                    'due_date' => $task->due_date?->format('Y-m-d'),
                    'created_at' => $task->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $task->updated_at->format('Y-m-d H:i:s'),
                ];
            });

            return ApiResponseService::success(
                [
                    'tasks' => $exportData,
                    'total_count' => $exportData->count(),
                    'exported_at' => now()->toISOString()
                ],
                'Tasks exported successfully'
            );

        } catch (\Exception $e) {
            return ApiResponseService::handleException($e);
        }
    }
}
