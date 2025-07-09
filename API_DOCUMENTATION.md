# Task Manager API Documentation

## Base URL
```
http://localhost:8000/api
```

## Response Format

All API responses follow this consistent format:

```json
{
    "status": true,
    "status_code": 200,
    "message": "Success message",
    "data": {...},
    "validation_errors": null,
    "timestamp": "2024-07-09T10:30:00.000000Z"
}
```

### Response Fields
- `status`: Boolean indicating success (true) or failure (false)
- `status_code`: HTTP status code
- `message`: Human-readable message
- `data`: Response data (null for errors)
- `validation_errors`: Validation error details (null for success)
- `timestamp`: ISO 8601 timestamp

## Authentication
No authentication required for this demo API.

## Endpoints

### 1. Health Check
**GET** `/api/health`

Check API health status.

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "API is healthy",
    "data": {
        "service": "Task Manager API",
        "version": "1.0.0",
        "timestamp": "2024-07-09T10:30:00.000000Z",
        "environment": "local"
    }
}
```

### 2. Get All Tasks
**GET** `/api/tasks`

Retrieve paginated list of tasks with optional filters.

**Query Parameters:**
- `page` (integer): Page number (default: 1)
- `per_page` (integer): Items per page (default: 10, max: 100)
- `status` (string): Filter by status (pending, in_progress, completed)
- `start_date` (date): Filter by due date from
- `end_date` (date): Filter by due date to
- `search` (string): Search in title and description
- `overdue` (boolean): Show only overdue tasks
- `sort_by` (string): Sort field (title, status, due_date, created_at)
- `sort_order` (string): Sort direction (asc, desc)

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Tasks retrieved successfully",
    "data": [...],
    "pagination": {
        "current_page": 1,
        "per_page": 10,
        "total": 25,
        "last_page": 3,
        "from": 1,
        "to": 10,
        "has_more_pages": true,
        "links": {...}
    }
}
```

### 3. Create Task
**POST** `/api/tasks`

Create a new task.

**Request Body:**
```json
{
    "title": "Task title",
    "description": "Task description (optional)",
    "status": "pending",
    "due_date": "2024-07-15"
}
```

**Validation Rules:**
- `title`: required, string, min:3, max:255
- `description`: optional, string, max:2000
- `status`: required, enum (pending, in_progress, completed)
- `due_date`: optional, date, must be today or future

**Response:**
```json
{
    "status": true,
    "status_code": 201,
    "message": "Task created successfully",
    "data": {
        "id": 1,
        "title": "Task title",
        "description": "Task description",
        "status": "pending",
        "due_date": "2024-07-15",
        "created_at": "2024-07-09T10:30:00.000000Z",
        "updated_at": "2024-07-09T10:30:00.000000Z"
    }
}
```

### 4. Get Single Task
**GET** `/api/tasks/{id}`

Retrieve a specific task by ID.

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Task retrieved successfully",
    "data": {
        "id": 1,
        "title": "Task title",
        "description": "Task description",
        "status": "pending",
        "due_date": "2024-07-15",
        "created_at": "2024-07-09T10:30:00.000000Z",
        "updated_at": "2024-07-09T10:30:00.000000Z"
    }
}
```

### 5. Update Task
**PUT/PATCH** `/api/tasks/{id}`

Update an existing task.

**Request Body:**
```json
{
    "title": "Updated title",
    "description": "Updated description",
    "status": "in_progress",
    "due_date": "2024-07-20"
}
```

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Task updated successfully",
    "data": {...}
}
```

### 6. Delete Task
**DELETE** `/api/tasks/{id}`

Delete a specific task.

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Task deleted successfully",
    "data": {
        "deleted_task_id": 1
    }
}
```

### 7. Task Statistics
**GET** `/api/tasks/statistics/overview`

Get task statistics overview.

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Task statistics retrieved successfully",
    "data": {
        "total": 50,
        "pending": 15,
        "in_progress": 20,
        "completed": 12,
        "overdue": 3
    }
}
```

### 8. Tasks by Status
**GET** `/api/tasks/status/{status}`

Get all tasks with specific status.

**Parameters:**
- `status`: pending, in_progress, or completed

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Tasks with status 'pending' retrieved successfully",
    "data": {
        "items": [...],
        "count": 15
    }
}
```

### 9. Overdue Tasks
**GET** `/api/tasks/filter/overdue`

Get all overdue tasks.

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Overdue tasks retrieved successfully",
    "data": {
        "items": [...],
        "count": 3
    }
}
```

### 10. Search Tasks
**GET** `/api/tasks/search/query`

Search tasks by title and/or description.

**Query Parameters:**
- `query` (required): Search term
- `fields` (optional): Array of fields to search (title, description)
- `per_page` (optional): Items per page

**Example:**
```
GET /api/tasks/search/query?query=urgent&fields[]=title&per_page=20
```

### 11. Mark Task as Completed
**PATCH** `/api/tasks/{id}/complete`

Mark a specific task as completed.

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Task marked as completed successfully",
    "data": {...}
}
```

### 12. Bulk Operations
**POST** `/api/tasks/bulk-action`

Perform bulk operations on multiple tasks.

**Request Body:**
```json
{
    "action": "mark_completed",
    "task_ids": [1, 2, 3, 4]
}
```

**Available Actions:**
- `delete`: Delete selected tasks
- `mark_completed`: Mark as completed
- `mark_pending`: Mark as pending
- `mark_in_progress`: Mark as in progress

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Successfully marked 4 task(s) as completed",
    "data": {
        "count": 4,
        "message": "Successfully marked 4 task(s) as completed",
        "action": "mark_completed"
    }
}
```

### 13. Export Tasks
**GET** `/api/tasks/export/data`

Export tasks data (with optional filters).

**Query Parameters:**
- `status` (optional): Filter by status
- `start_date` (optional): Filter by due date from
- `end_date` (optional): Filter by due date to

**Response:**
```json
{
    "status": true,
    "status_code": 200,
    "message": "Tasks exported successfully",
    "data": {
        "tasks": [...],
        "total_count": 25,
        "exported_at": "2024-07-09T10:30:00.000000Z"
    }
}
```

## Error Responses

### Validation Error (422)
```json
{
    "status": false,
    "status_code": 422,
    "message": "Validation failed",
    "data": null,
    "validation_errors": {
        "title": ["The title field is required."],
        "status": ["Invalid status selected."]
    }
}
```

### Not Found (404)
```json
{
    "status": false,
    "status_code": 404,
    "message": "Task not found",
    "data": null,
    "validation_errors": null
}
```

### Server Error (500)
```json
{
    "status": false,
    "status_code": 500,
    "message": "An error occurred while processing your request",
    "data": null,
    "validation_errors": null
}
```

## Example Usage

### JavaScript/Fetch
```javascript
// Get all tasks
const response = await fetch('/api/tasks');
const data = await response.json();

// Create task
const newTask = await fetch('/api/tasks', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    },
    body: JSON.stringify({
        title: 'New Task',
        status: 'pending',
        due_date: '2024-07-15'
    })
});
```

### cURL
```bash
# Get tasks
curl -X GET "http://localhost:8000/api/tasks"

# Create task
curl -X POST "http://localhost:8000/api/tasks" \
  -H "Content-Type: application/json" \
  -d '{"title":"New Task","status":"pending","due_date":"2024-07-15"}'

# Update task
curl -X PUT "http://localhost:8000/api/tasks/1" \
  -H "Content-Type: application/json" \
  -d '{"title":"Updated Task","status":"completed"}'
```

## Rate Limiting
No rate limiting implemented for this demo API.

## CORS
CORS is configured to allow requests from any origin for development purposes.
