<?php

namespace App\Services;

use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class TaskValidationService
{
    /**
     * Validate task creation data
     */
    public function validateTaskCreation(array $data): array
    {
        return $this->validateTaskData($data, true);
    }

    /**
     * Validate task update data
     */
    public function validateTaskUpdate(array $data): array
    {
        return $this->validateTaskData($data, false);
    }

    /**
     * Validate bulk operation data
     */
    public function validateBulkOperation(array $data): array
    {
        $rules = [
            'action' => 'required|in:delete,mark_completed,mark_pending,mark_in_progress',
            'task_ids' => 'required|array|min:1',
            'task_ids.*' => 'exists:tasks,id'
        ];

        $messages = [
            'action.required' => 'Please select an action to perform.',
            'action.in' => 'Invalid action selected.',
            'task_ids.required' => 'Please select at least one task.',
            'task_ids.min' => 'Please select at least one task.',
            'task_ids.*.exists' => 'One or more selected tasks do not exist.',
        ];

        return $this->performValidation($data, $rules, $messages);
    }

    /**
     * Validate status change
     */
    public function validateStatusChange(string $status): void
    {
        if (!in_array($status, $this->getAllowedStatuses())) {
            throw new \InvalidArgumentException(
                'Invalid status provided. Allowed statuses are: ' . implode(', ', $this->getAllowedStatuses())
            );
        }
    }

    /**
     * Validate search and filter parameters
     */
    public function validateSearchFilters(array $data): array
    {
        $rules = [
            'search' => 'nullable|string|max:255',
            'status' => ['nullable', Rule::in($this->getAllowedStatuses())],
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'overdue' => 'nullable|boolean',
            'sort_by' => ['nullable', Rule::in($this->getAllowedSortFields())],
            'sort_order' => 'nullable|in:asc,desc',
        ];

        $messages = [
            'search.max' => 'Search term cannot exceed 255 characters.',
            'status.in' => 'Invalid status filter selected.',
            'start_date.date' => 'Please enter a valid start date.',
            'end_date.date' => 'Please enter a valid end date.',
            'end_date.after_or_equal' => 'End date must be after or equal to start date.',
            'sort_by.in' => 'Invalid sort field selected.',
            'sort_order.in' => 'Sort order must be either ascending or descending.',
        ];

        return $this->performValidation($data, $rules, $messages);
    }

    /**
     * Validate pagination parameters
     */
    public function validatePagination(array $data): array
    {
        $rules = [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
        ];

        $messages = [
            'page.integer' => 'Page must be a valid number.',
            'page.min' => 'Page must be at least 1.',
            'per_page.integer' => 'Items per page must be a valid number.',
            'per_page.min' => 'Items per page must be at least 1.',
            'per_page.max' => 'Items per page cannot exceed 100.',
        ];

        return $this->performValidation($data, $rules, $messages);
    }

    /**
     * Core task data validation
     */
    private function validateTaskData(array $data, bool $isCreating = true): array
    {
        $rules = [
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:2000',
            'status' => ['required', Rule::in($this->getAllowedStatuses())],
            'due_date' => $this->getDueDateValidationRule($isCreating),
            'priority' => 'nullable|in:low,medium,high',
        ];

        $messages = [
            'title.required' => 'The task title is required.',
            'title.min' => 'The task title must be at least 3 characters.',
            'title.max' => 'The task title cannot exceed 255 characters.',
            'description.max' => 'The task description cannot exceed 2000 characters.',
            'status.required' => 'Please select a task status.',
            'status.in' => 'Invalid status selected. Please choose from: ' . implode(', ', $this->getAllowedStatuses()),
            'due_date.after_or_equal' => 'The due date cannot be in the past.',
            'due_date.date' => 'Please enter a valid date.',
            'priority.in' => 'Invalid priority selected. Please choose from: low, medium, high.',
        ];

        return $this->performValidation($data, $rules, $messages);
    }

    /**
     * Get due date validation rule based on context
     */
    private function getDueDateValidationRule(bool $isCreating): string
    {
        return $isCreating 
            ? 'nullable|date|after_or_equal:today' 
            : 'nullable|date';
    }

    /**
     * Perform validation and return validated data
     */
    private function performValidation(array $data, array $rules, array $messages): array
    {
        $validator = Validator::make($data, $rules, $messages);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return $validator->validated();
    }

    /**
     * Get allowed task statuses
     */
    public function getAllowedStatuses(): array
    {
        return ['pending', 'in_progress', 'completed'];
    }

    /**
     * Get allowed sort fields
     */
    public function getAllowedSortFields(): array
    {
        return ['title', 'status', 'due_date', 'created_at', 'updated_at', 'priority'];
    }

    /**
     * Get allowed priority levels
     */
    public function getAllowedPriorities(): array
    {
        return ['low', 'medium', 'high'];
    }

    /**
     * Get validation rules for API endpoints
     */
    public function getApiValidationRules(bool $isCreating = true): array
    {
        return [
            'title' => 'required|string|max:255|min:3',
            'description' => 'nullable|string|max:2000',
            'status' => ['required', Rule::in($this->getAllowedStatuses())],
            'due_date' => $this->getDueDateValidationRule($isCreating),
            'priority' => 'nullable|in:low,medium,high',
        ];
    }

    /**
     * Get validation messages for API endpoints
     */
    public function getApiValidationMessages(): array
    {
        return [
            'title.required' => 'Task title is required',
            'title.min' => 'Task title must be at least 3 characters',
            'title.max' => 'Task title cannot exceed 255 characters',
            'description.max' => 'Task description cannot exceed 2000 characters',
            'status.required' => 'Task status is required',
            'status.in' => 'Invalid status. Allowed values: ' . implode(', ', $this->getAllowedStatuses()),
            'due_date.after_or_equal' => 'Due date cannot be in the past',
            'due_date.date' => 'Invalid date format',
            'priority.in' => 'Invalid priority. Allowed values: ' . implode(', ', $this->getAllowedPriorities()),
        ];
    }

    /**
     * Validate task title uniqueness (optional business rule)
     */
    public function validateTitleUniqueness(string $title, ?int $excludeId = null): bool
    {
        $query = \App\Models\Task::where('title', $title);
        
        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }
        
        return $query->doesntExist();
    }

    /**
     * Validate business rules for task completion
     */
    public function validateTaskCompletion(\App\Models\Task $task): void
    {
        if ($task->status === 'completed') {
            throw new ValidationException(
                Validator::make([], []),
                ['status' => ['Task is already completed']]
            );
        }

        // Additional business rules can be added here
        // For example: check if all subtasks are completed
        // Or check if required documents are uploaded
    }

    /**
     * Validate business rules for task deletion
     */
    public function validateTaskDeletion(\App\Models\Task $task): void
    {
        // Add business rules for deletion
        // For example: prevent deletion of tasks that are in progress
        // Or tasks that have dependencies
        
        if ($task->status === 'in_progress') {
            throw new ValidationException(
                Validator::make([], []),
                ['deletion' => ['Cannot delete tasks that are in progress']]
            );
        }
    }
}
