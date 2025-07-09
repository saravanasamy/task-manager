@extends('layouts.app')

@section('title', 'All Tasks')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="bi bi-list-task"></i> Task Manager</h1>
            <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> New Task
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $statistics['total'] }}</h4>
                                <p class="mb-0">Total Tasks</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-list-task display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $statistics['pending'] }}</h4>
                                <p class="mb-0">Pending</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-clock display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $statistics['in_progress'] }}</h4>
                                <p class="mb-0">In Progress</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-arrow-repeat display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <div class="d-flex justify-content-between">
                            <div>
                                <h4 class="mb-0">{{ $statistics['completed'] }}</h4>
                                <p class="mb-0">Completed</p>
                            </div>
                            <div class="align-self-center">
                                <i class="bi bi-check-circle display-6"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($statistics['overdue'] > 0)
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle"></i>
                You have <strong>{{ $statistics['overdue'] }}</strong> overdue task(s). 
                <a href="{{ request()->fullUrlWithQuery(['overdue' => '1']) }}" class="alert-link">View overdue tasks</a>
            </div>
        @endif

        <!-- Filters -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><i class="bi bi-funnel"></i> Filters & Search</h5>
            </div>
            <div class="card-body">
                <form method="GET" action="{{ route('tasks.index') }}" class="row g-3">
                    <div class="col-md-2">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Search tasks...">
                    </div>
                    <div class="col-md-2">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="in_progress" {{ request('status') === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label for="start_date" class="form-label">Due Date From</label>
                        <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label for="end_date" class="form-label">Due Date To</label>
                        <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-2">
                        <div class="form-check mt-4">
                            <input class="form-check-input" type="checkbox" name="overdue" value="1" id="overdue" 
                                   {{ request('overdue') ? 'checked' : '' }}>
                            <label class="form-check-label" for="overdue">
                                Show only overdue
                            </label>
                        </div>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-outline-primary me-2">
                            <i class="bi bi-search"></i> Filter
                        </button>
                        <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle"></i> Clear
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Task List -->
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Tasks ({{ $tasks->total() }} total)</h5>
                <div class="d-flex gap-2">
                    <small class="text-muted">Sort by:</small>
                    @php
                        $currentSort = request('sort_by', 'created_at');
                        $currentOrder = request('sort_order', 'desc');
                        $newOrder = $currentOrder === 'asc' ? 'desc' : 'asc';
                    @endphp
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'title', 'sort_order' => $currentSort === 'title' ? $newOrder : 'asc']) }}" 
                       class="sort-link {{ $currentSort === 'title' ? 'sort-active' : '' }}">
                        Title {!! $currentSort === 'title' ? ($currentOrder === 'asc' ? '<i class="bi bi-arrow-up"></i>' : '<i class="bi bi-arrow-down"></i>') : '' !!}
                    </a>
                    |
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'status', 'sort_order' => $currentSort === 'status' ? $newOrder : 'asc']) }}" 
                       class="sort-link {{ $currentSort === 'status' ? 'sort-active' : '' }}">
                        Status {!! $currentSort === 'status' ? ($currentOrder === 'asc' ? '<i class="bi bi-arrow-up"></i>' : '<i class="bi bi-arrow-down"></i>') : '' !!}
                    </a>
                    |
                    <a href="{{ request()->fullUrlWithQuery(['sort_by' => 'due_date', 'sort_order' => $currentSort === 'due_date' ? $newOrder : 'asc']) }}" 
                       class="sort-link {{ $currentSort === 'due_date' ? 'sort-active' : '' }}">
                        Due Date {!! $currentSort === 'due_date' ? ($currentOrder === 'asc' ? '<i class="bi bi-arrow-up"></i>' : '<i class="bi bi-arrow-down"></i>') : '' !!}
                    </a>
                </div>
            </div>
            <div class="card-body p-0">
                @if($tasks->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Title</th>
                                    <th>Status</th>
                                    <th>Due Date</th>
                                    <th>Created</th>
                                    <th width="150">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($tasks as $task)
                                    <tr>
                                        <td>
                                            <div>
                                                <strong>{{ $task->title }}</strong>
                                                @if($task->description)
                                                    <br>
                                                    <small class="text-muted">{{ Str::limit($task->description, 80) }}</small>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $task->status_color }} badge-status">
                                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($task->due_date)
                                                {{ $task->due_date->format('M d, Y') }}
                                                @if($task->due_date->isPast() && $task->status !== 'completed')
                                                    <span class="badge bg-danger ms-1">Overdue</span>
                                                @endif
                                            @else
                                                <span class="text-muted">No due date</span>
                                            @endif
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $task->created_at->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{ route('tasks.show', $task) }}" class="btn btn-outline-info" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('tasks.edit', $task) }}" class="btn btn-outline-warning" title="Edit">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this task?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-outline-danger" title="Delete">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-3">
                        {{ $tasks->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox display-1 text-muted"></i>
                        <h4 class="mt-3">No tasks found</h4>
                        <p class="text-muted">Get started by creating your first task!</p>
                        <a href="{{ route('tasks.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Create Task
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
