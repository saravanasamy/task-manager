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
     * @OA\Get(
     *     path="/api/tasks",
     *     operationId="getTasksList",
     *     tags={"Tasks"},
     *     summary="Get list of tasks",
     *     description="Returns paginated list of tasks with optional filtering and sorting",
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Page number",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, example=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=10)
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "completed"})
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search in title and description",
     *         required=false,
     *         @OA\Schema(type="string", example="documentation")
     *     ),
     *     @OA\Parameter(
     *         name="sort_by",
     *         in="query",
     *         description="Sort by field",
     *         required=false,
     *         @OA\Schema(type="string", enum={"title", "status", "due_date", "created_at"})
     *     ),
     *     @OA\Parameter(
     *         name="sort_order",
     *         in="query",
     *         description="Sort order",
     *         required=false,
     *         @OA\Schema(type="string", enum={"asc", "desc"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Tasks retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="title", type="string", example="Sample Task"),
     *                         @OA\Property(property="description", type="string", example="Task description"),
     *                         @OA\Property(property="status", type="string", example="pending"),
     *                         @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2024-07-09T10:30:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2024-07-09T10:30:00.000000Z")
     *                     )
     *                 ),
     *                 @OA\Property(property="total", type="integer", example=50)
     *             ),
     *             @OA\Property(property="timestamp", type="string", format="date-time", example="2024-07-09T10:30:00.000000Z")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=422), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="validation_errors", type="object"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/tasks",
     *     operationId="createTask",
     *     tags={"Tasks"},
     *     summary="Create a new task",
     *     description="Create a new task with validation",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task data",
     *         @OA\JsonContent(
     *             required={"title", "status"},
     *             @OA\Property(property="title", type="string", maxLength=255, example="Complete project documentation"),
     *             @OA\Property(property="description", type="string", maxLength=1000, example="Write comprehensive documentation for the task manager project"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="pending"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2024-07-15")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=201),
     *             @OA\Property(property="message", type="string", example="Task created successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Sample Task"), @OA\Property(property="description", type="string", example="Task description"), @OA\Property(property="status", type="string", example="pending"), @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time")),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=422), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="validation_errors", type="object"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     )
     * )
     *
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
     * @OA\Get(
     *     path="/api/tasks/{id}",
     *     operationId="getTask",
     *     tags={"Tasks"},
     *     summary="Get a specific task",
     *     description="Retrieve a task by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Task retrieved successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Sample Task"), @OA\Property(property="description", type="string", example="Task description"), @OA\Property(property="status", type="string", example="pending"), @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time")),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=404), @OA\Property(property="message", type="string", example="Resource not found"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     )
     * )
     *
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
     * @OA\Put(
     *     path="/api/tasks/{id}",
     *     operationId="updateTask",
     *     tags={"Tasks"},
     *     summary="Update a task",
     *     description="Update an existing task",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated task data",
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string", maxLength=255, example="Updated task title"),
     *             @OA\Property(property="description", type="string", maxLength=1000, example="Updated description"),
     *             @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="in_progress"),
     *             @OA\Property(property="due_date", type="string", format="date", example="2024-07-20")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Task updated successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Sample Task"), @OA\Property(property="description", type="string", example="Task description"), @OA\Property(property="status", type="string", example="pending"), @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time")),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=404), @OA\Property(property="message", type="string", example="Resource not found"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=422), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="validation_errors", type="object"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     )
     * )
     *
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
     * @OA\Delete(
     *     path="/api/tasks/{id}",
     *     operationId="deleteTask",
     *     tags={"Tasks"},
     *     summary="Delete a task",
     *     description="Delete an existing task",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Task deleted successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="deleted_task_id", type="integer", example=1)
     *             ),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=404), @OA\Property(property="message", type="string", example="Resource not found"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     )
     * )
     *
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
     * @OA\Get(
     *     path="/api/tasks/statistics",
     *     operationId="getTaskStatistics",
     *     tags={"Statistics"},
     *     summary="Get task statistics",
     *     description="Retrieve comprehensive task statistics",
     *     @OA\Response(
     *         response=200,
     *         description="Statistics retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Task statistics retrieved successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="total", type="integer", example=47), @OA\Property(property="pending", type="integer", example=15), @OA\Property(property="in_progress", type="integer", example=12), @OA\Property(property="completed", type="integer", example=20), @OA\Property(property="overdue", type="integer", example=5)),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     )
     * )
     *
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
     * @OA\Get(
     *     path="/api/tasks/status/{status}",
     *     operationId="getTasksByStatus",
     *     tags={"Tasks"},
     *     summary="Get tasks by status",
     *     description="Retrieve all tasks with a specific status",
     *     @OA\Parameter(
     *         name="status",
     *         in="path",
     *         description="Task status",
     *         required=true,
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "completed"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tasks retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Tasks retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Sample Task"), @OA\Property(property="description", type="string", example="Task description"), @OA\Property(property="status", type="string", example="pending"), @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
     *             ),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid status",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=422), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="validation_errors", type="object"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     )
     * )
     *
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
     * @OA\Get(
     *     path="/api/tasks/overdue",
     *     operationId="getOverdueTasks",
     *     tags={"Tasks"},
     *     summary="Get overdue tasks",
     *     description="Retrieve all tasks that are past their due date",
     *     @OA\Response(
     *         response=200,
     *         description="Overdue tasks retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Overdue tasks retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Sample Task"), @OA\Property(property="description", type="string", example="Task description"), @OA\Property(property="status", type="string", example="pending"), @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
     *             ),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     )
     * )
     *
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
     * @OA\Put(
     *     path="/api/tasks/{id}/complete",
     *     operationId="markTaskCompleted",
     *     tags={"Tasks"},
     *     summary="Mark task as completed",
     *     description="Update task status to completed",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="Task ID",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task marked as completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Task marked as completed successfully"),
     *             @OA\Property(property="data", type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Sample Task"), @OA\Property(property="description", type="string", example="Task description"), @OA\Property(property="status", type="string", example="pending"), @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time")),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Task not found",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=404), @OA\Property(property="message", type="string", example="Resource not found"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Cannot mark task as completed",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=422), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="validation_errors", type="object"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     )
     * )
     *
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
     * @OA\Post(
     *     path="/api/tasks/bulk",
     *     operationId="bulkTaskActions",
     *     tags={"Tasks"},
     *     summary="Bulk operations on tasks",
     *     description="Perform bulk operations like delete, update status on multiple tasks",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Bulk operation data",
     *         @OA\JsonContent(
     *             required={"action", "task_ids"},
     *             @OA\Property(property="action", type="string", enum={"delete", "update_status"}, example="update_status"),
     *             @OA\Property(
     *                 property="task_ids",
     *                 type="array",
     *                 @OA\Items(type="integer"),
     *                 example={1, 2, 3}
     *             ),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="status", type="string", enum={"pending", "in_progress", "completed"}, example="completed")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Bulk operation completed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Bulk operation completed successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="processed", type="integer", example=3),
     *                 @OA\Property(property="failed", type="integer", example=0),
     *                 @OA\Property(property="message", type="string", example="3 tasks updated successfully")
     *             ),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=422), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="validation_errors", type="object"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     )
     * )
     *
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
     * @OA\Get(
     *     path="/api/tasks/search",
     *     operationId="searchTasks",
     *     tags={"Tasks"},
     *     summary="Search tasks",
     *     description="Search for tasks using various criteria",
     *     @OA\Parameter(
     *         name="query",
     *         in="query",
     *         description="Search query",
     *         required=true,
     *         @OA\Schema(type="string", example="documentation")
     *     ),
     *     @OA\Parameter(
     *         name="fields",
     *         in="query",
     *         description="Fields to search in",
     *         required=false,
     *         @OA\Schema(
     *             type="array",
     *             @OA\Items(type="string", enum={"title", "description"}),
     *             example={"title", "description"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", minimum=1, maximum=100, example=10)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Search results retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Search results retrieved successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=1),
     *                 @OA\Property(
     *                     property="data",
     *                     type="array",
     *                     @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Sample Task"), @OA\Property(property="description", type="string", example="Task description"), @OA\Property(property="status", type="string", example="pending"), @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
     *                 ),
     *                 @OA\Property(property="total", type="integer", example=10)
     *             ),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid search parameters",
     *         @OA\JsonContent(type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=422), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="validation_errors", type="object"), @OA\Property(property="timestamp", type="string", format="date-time"))
     *     )
     * )
     *
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
     * @OA\Get(
     *     path="/api/tasks/export",
     *     operationId="exportTasks",
     *     tags={"Tasks"},
     *     summary="Export tasks",
     *     description="Export tasks data with optional filtering",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status",
     *         required=false,
     *         @OA\Schema(type="string", enum={"pending", "in_progress", "completed"})
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter by due date from",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-07-01")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter by due date to",
     *         required=false,
     *         @OA\Schema(type="string", format="date", example="2024-12-31")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Tasks exported successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="status_code", type="integer", example=200),
     *             @OA\Property(property="message", type="string", example="Tasks exported successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="tasks",
     *                     type="array",
     *                     @OA\Items(type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Sample Task"), @OA\Property(property="description", type="string", example="Task description"), @OA\Property(property="status", type="string", example="pending"), @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time"))
     *                 ),
     *                 @OA\Property(property="total_count", type="integer", example=25),
     *                 @OA\Property(property="exported_at", type="string", format="date-time", example="2024-07-09T10:30:00Z")
     *             ),
     *             @OA\Property(property="timestamp", type="string", format="date-time")
     *         )
     *     )
     * )
     *
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

