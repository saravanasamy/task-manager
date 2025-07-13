<?php

namespace App\Services;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class TaskService
{
    protected TaskValidationService $validationService;

    public function __construct(TaskValidationService $validationService)
    {
        $this->validationService = $validationService;
    }

    /**
     * Get validation service (for API controller access)
     */
    public function getValidationService(): TaskValidationService
    {
        return $this->validationService;
    }

    /**
     * Get paginated tasks with filters and sorting
     */
    public function getAllTasks(Request $request): LengthAwarePaginator
    {
        // Validate search and filter parameters
        $validated = $this->validationService->validateSearchFilters($request->all());
        
        $query = Task::query();

        // Apply filters
        $this->applyFilters($query, $request);

        // Apply sorting
        $this->applySorting($query, $request);

        $perPage = (int) $request->get('per_page', 10);
        $page = (int) $request->get('page', 1);
        return $query->paginate($perPage, ['*'], 'page', $page)->withQueryString();
    }

    /**
     * Create a new task
     */
    public function createTask(array $data): Task
    {
        $validatedData = $this->validationService->validateTaskCreation($data);
        
        return Task::create($validatedData);
    }

    /**
     * Update an existing task
     */
    public function updateTask(Task $task, array $data): Task
    {
        $validatedData = $this->validationService->validateTaskUpdate($data);
        
        $task->update($validatedData);
        
        return $task->fresh();
    }

    /**
     * Delete a task
     */
    public function deleteTask(Task $task): bool
    {
        // Validate business rules for deletion
        $this->validationService->validateTaskDeletion($task);
        
        return $task->delete();
    }

    /**
     * Get task statistics
     */
    public function getTaskStatistics(): array
    {
        return [
            'total' => Task::count(),
            'pending' => Task::where('status', 'pending')->count(),
            'in_progress' => Task::where('status', 'in_progress')->count(),
            'completed' => Task::where('status', 'completed')->count(),
            'overdue' => Task::where('due_date', '<', now())
                ->whereIn('status', ['pending', 'in_progress'])
                ->count(),
        ];
    }

    /**
     * Get tasks by status
     */
    public function getTasksByStatus(string $status): \Illuminate\Database\Eloquent\Collection
    {
        return Task::where('status', $status)->get();
    }

    /**
     * Get overdue tasks
     */
    public function getOverdueTasks(): \Illuminate\Database\Eloquent\Collection
    {
        return Task::where('due_date', '<', now())
            ->whereIn('status', ['pending', 'in_progress'])
            ->get();
    }

    /**
     * Mark task as completed
     */
    public function markAsCompleted(Task $task): Task
    {
        // Validate business rules for completion
        $this->validationService->validateTaskCompletion($task);
        
        $task->update(['status' => 'completed']);
        
        return $task->fresh();
    }

    /**
     * Apply filters to the query
     */
    private function applyFilters($query, Request $request): void
    {
        // Filter by status
        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        // Filter by due date range
        if ($request->filled('start_date') || $request->filled('end_date')) {
            $query->byDueDateRange($request->start_date, $request->end_date);
        }

        // Filter overdue tasks
        if ($request->filled('overdue') && $request->overdue === '1') {
            $query->where('due_date', '<', now())
                ->whereIn('status', ['pending', 'in_progress']);
        }

        // Search in title and description
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', "%{$searchTerm}%")
                  ->orWhere('description', 'like', "%{$searchTerm}%");
            });
        }
    }

    /**
     * Apply sorting to the query
     */
    private function applySorting($query, Request $request): void
    {
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        
        $allowedSorts = $this->validationService->getAllowedSortFields();
        
        if (in_array($sortBy, $allowedSorts)) {
            $query->orderBy($sortBy, $sortOrder);
        } else {
            $query->orderBy('created_at', 'desc');
        }
    }

    /**
     * Bulk operations
     */
    public function bulkUpdateStatus(array $taskIds, string $status): int
    {
        // Validate status
        $this->validationService->validateStatusChange($status);

        return Task::whereIn('id', $taskIds)->update(['status' => $status]);
    }

    /**
     * Bulk delete tasks
     */
    public function bulkDelete(array $taskIds): int
    {
        // Validate each task before deletion
        $tasks = Task::whereIn('id', $taskIds)->get();
        foreach ($tasks as $task) {
            $this->validationService->validateTaskDeletion($task);
        }

        return Task::whereIn('id', $taskIds)->delete();
    }

    /**
     * Process bulk operations
     */
    public function processBulkOperation(array $data): array
    {
        $validated = $this->validationService->validateBulkOperation($data);
        
        $taskIds = $validated['task_ids'];
        $action = $validated['action'];
        $count = 0;
        $message = '';

        switch ($action) {
            case 'delete':
                $count = $this->bulkDelete($taskIds);
                $message = "Successfully deleted {$count} task(s).";
                break;
            case 'mark_completed':
                $count = $this->bulkUpdateStatus($taskIds, 'completed');
                $message = "Successfully marked {$count} task(s) as completed.";
                break;
            case 'mark_pending':
                $count = $this->bulkUpdateStatus($taskIds, 'pending');
                $message = "Successfully marked {$count} task(s) as pending.";
                break;
            case 'mark_in_progress':
                $count = $this->bulkUpdateStatus($taskIds, 'in_progress');
                $message = "Successfully marked {$count} task(s) as in progress.";
                break;
        }

        return [
            'count' => $count,
            'message' => $message,
            'action' => $action
        ];
    }
}
