@extends('layouts.app')

@section('title', 'View Task')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><i class="bi bi-eye"></i> Task Details</h4>
                <span class="badge bg-{{ $task->status_color }} fs-6">
                    {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                </span>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-12">
                        <h2 class="mb-3">{{ $task->title }}</h2>
                    </div>
                </div>

                @if($task->description)
                    <div class="row mb-4">
                        <div class="col-12">
                            <h5><i class="bi bi-journal-text"></i> Description</h5>
                            <div class="bg-light p-3 rounded">
                                {{ $task->description }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong><i class="bi bi-flag"></i> Status:</strong>
                            <span class="badge bg-{{ $task->status_color }} ms-2">
                                {{ ucfirst(str_replace('_', ' ', $task->status)) }}
                            </span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong><i class="bi bi-calendar-event"></i> Due Date:</strong>
                            @if($task->due_date)
                                <span class="ms-2">{{ $task->due_date->format('F d, Y') }}</span>
                                @if($task->due_date->isPast() && $task->status !== 'completed')
                                    <span class="badge bg-danger ms-2">Overdue</span>
                                @elseif($task->due_date->isToday())
                                    <span class="badge bg-warning ms-2">Due Today</span>
                                @elseif($task->due_date->isTomorrow())
                                    <span class="badge bg-info ms-2">Due Tomorrow</span>
                                @endif
                            @else
                                <span class="text-muted ms-2">No due date set</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong><i class="bi bi-plus-circle"></i> Created:</strong>
                            <span class="ms-2">{{ $task->created_at->format('F d, Y \a\t g:i A') }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-item mb-3">
                            <strong><i class="bi bi-pencil"></i> Last Updated:</strong>
                            <span class="ms-2">{{ $task->updated_at->format('F d, Y \a\t g:i A') }}</span>
                        </div>
                    </div>
                </div>

                @if($task->due_date)
                    <div class="row">
                        <div class="col-12">
                            <div class="info-item mb-3">
                                <strong><i class="bi bi-clock"></i> Time Remaining:</strong>
                                <span class="ms-2">
                                    @if($task->status === 'completed')
                                        <span class="text-success">Task Completed</span>
                                    @elseif($task->due_date->isPast())
                                        <span class="text-danger">Overdue by {{ $task->due_date->diffForHumans() }}</span>
                                    @else
                                        <span class="text-info">{{ $task->due_date->diffForHumans() }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                    <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Back to Tasks
                    </a>
                    <div>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-warning me-2">
                            <i class="bi bi-pencil"></i> Edit Task
                        </a>
                        <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to delete this task? This action cannot be undone.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Delete Task
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
