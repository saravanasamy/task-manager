<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Services\TaskService;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    protected TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $tasks = $this->taskService->getAllTasks($request);
            $statistics = $this->taskService->getTaskStatistics();

            return view('tasks.index', compact('tasks', 'statistics'));
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to load tasks. Please try again.');
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tasks.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        try {
            $this->taskService->createTask($request->validated());

            return redirect()->route('tasks.index')
                ->with('success', 'Task created successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create task. Please try again.');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return view('tasks.show', compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return view('tasks.edit', compact('task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task)
    {
        try {
            $this->taskService->updateTask($task, $request->validated());

            return redirect()->route('tasks.index')
                ->with('success', 'Task updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update task. Please try again.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $this->taskService->deleteTask($task);

            return redirect()->route('tasks.index')
                ->with('success', 'Task deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete task. Please try again.');
        }
    }

    /**
     * Mark task as completed
     */
    public function markCompleted(Task $task)
    {
        try {
            $this->taskService->markAsCompleted($task);

            return redirect()->back()
                ->with('success', 'Task marked as completed!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to update task status. Please try again.');
        }
    }

    /**
     * Bulk operations
     */
    public function bulkAction(Request $request)
    {
        try {
            $result = $this->taskService->processBulkOperation($request->all());

            return redirect()->route('tasks.index')
                ->with('success', $result['message']);
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to perform bulk operation. Please try again.');
        }
    }
}
