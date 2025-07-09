# Swagger API Documentation

This document provides information about the Swagger/OpenAPI documentation for the Laravel Task Manager API.

## Overview

The Laravel Task Manager API is fully documented using Swagger/OpenAPI 3.0 specification with the L5-Swagger package. This provides interactive API documentation that allows you to explore, test, and understand the API endpoints.

## Accessing the Documentation

### Web Interface
Once the application is running, you can access the interactive Swagger documentation at:

```
http://localhost:8000/api/documentation
```

Alternative URL (if configured):
```
http://localhost:8000/api/docs
```

### JSON Specification
The raw OpenAPI JSON specification is available at:
```
http://localhost:8000/api/docs.json
```

## Features

### Interactive Documentation
- **Try It Out**: Test API endpoints directly from the documentation
- **Request/Response Examples**: See sample requests and responses
- **Schema Validation**: View request/response schemas with validation rules
- **Authentication**: Test authenticated endpoints (when implemented)

### Comprehensive Coverage
The documentation includes:

#### Core CRUD Operations
- `GET /api/tasks` - List tasks with filtering and pagination
- `POST /api/tasks` - Create a new task
- `GET /api/tasks/{id}` - Get a specific task
- `PUT /api/tasks/{id}` - Update a task
- `DELETE /api/tasks/{id}` - Delete a task

#### Advanced Features
- `GET /api/tasks/statistics` - Get task statistics
- `GET /api/tasks/status/{status}` - Get tasks by status
- `GET /api/tasks/overdue` - Get overdue tasks
- `PUT /api/tasks/{id}/complete` - Mark task as completed
- `POST /api/tasks/bulk` - Bulk operations on tasks
- `GET /api/tasks/search` - Search tasks
- `GET /api/tasks/export` - Export tasks data

## API Response Format

All API responses follow a standardized format:

### Success Response
```json
{
    "status": "success",
    "status_code": 200,
    "message": "Operation completed successfully",
    "data": {
        // Response data here
    },
    "timestamp": "2024-07-09T10:30:00Z"
}
```

### Error Response
```json
{
    "status": "error",
    "status_code": 400,
    "message": "Error description",
    "data": null,
    "errors": {
        // Validation errors (if applicable)
    },
    "timestamp": "2024-07-09T10:30:00Z"
}
```

### Paginated Response
```json
{
    "status": "success",
    "status_code": 200,
    "message": "Data retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            // Array of items
        ],
        "first_page_url": "http://localhost:8000/api/tasks?page=1",
        "from": 1,
        "last_page": 5,
        "last_page_url": "http://localhost:8000/api/tasks?page=5",
        "next_page_url": "http://localhost:8000/api/tasks?page=2",
        "path": "http://localhost:8000/api/tasks",
        "per_page": 10,
        "prev_page_url": null,
        "to": 10,
        "total": 45
    },
    "timestamp": "2024-07-09T10:30:00Z"
}
```

## Data Schemas

### Task Schema
```json
{
    "id": 1,
    "title": "Complete project documentation",
    "description": "Write comprehensive documentation for the task manager project",
    "status": "in_progress",
    "due_date": "2024-07-15",
    "created_at": "2024-07-09T10:00:00Z",
    "updated_at": "2024-07-09T10:00:00Z"
}
```

### Task Status Values
- `pending` - Task is created but not started
- `in_progress` - Task is currently being worked on
- `completed` - Task has been finished

### Validation Rules

#### Create/Update Task
- `title`: Required, string, max 255 characters
- `description`: Optional, string, max 1000 characters
- `status`: Required, enum (pending, in_progress, completed)
- `due_date`: Optional, date format (Y-m-d), must be today or future

#### Filtering Parameters
- `status`: Optional, enum (pending, in_progress, completed)
- `search`: Optional, string, searches in title and description
- `start_date`: Optional, date format (Y-m-d)
- `end_date`: Optional, date format (Y-m-d)
- `sort_by`: Optional, enum (title, status, due_date, created_at, updated_at)
- `sort_order`: Optional, enum (asc, desc)
- `overdue`: Optional, boolean
- `page`: Optional, integer, minimum 1
- `per_page`: Optional, integer, minimum 1, maximum 100

## Usage Examples

### Basic Operations

#### Get All Tasks
```bash
curl -X GET "http://localhost:8000/api/tasks" \
     -H "Accept: application/json"
```

#### Create a Task
```bash
curl -X POST "http://localhost:8000/api/tasks" \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -d '{
         "title": "New Task",
         "description": "Task description",
         "status": "pending",
         "due_date": "2024-07-20"
     }'
```

#### Update a Task
```bash
curl -X PUT "http://localhost:8000/api/tasks/1" \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -d '{
         "title": "Updated Task",
         "status": "in_progress"
     }'
```

#### Delete a Task
```bash
curl -X DELETE "http://localhost:8000/api/tasks/1" \
     -H "Accept: application/json"
```

### Advanced Operations

#### Get Tasks with Filters
```bash
curl -X GET "http://localhost:8000/api/tasks?status=pending&sort_by=due_date&sort_order=asc&per_page=20" \
     -H "Accept: application/json"
```

#### Search Tasks
```bash
curl -X GET "http://localhost:8000/api/tasks/search?query=documentation&fields[]=title&fields[]=description" \
     -H "Accept: application/json"
```

#### Get Task Statistics
```bash
curl -X GET "http://localhost:8000/api/tasks/statistics" \
     -H "Accept: application/json"
```

#### Bulk Operations
```bash
curl -X POST "http://localhost:8000/api/tasks/bulk" \
     -H "Accept: application/json" \
     -H "Content-Type: application/json" \
     -d '{
         "action": "update_status",
         "task_ids": [1, 2, 3],
         "data": {
             "status": "completed"
         }
     }'
```

## Error Handling

The API uses standard HTTP status codes:

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `404` - Not Found
- `422` - Unprocessable Entity (Validation Error)
- `500` - Internal Server Error

### Common Error Responses

#### Validation Error (422)
```json
{
    "status": "error",
    "status_code": 422,
    "message": "Validation failed",
    "data": null,
    "validation_errors": {
        "title": ["The title field is required."],
        "due_date": ["The due date must be a valid date."]
    },
    "timestamp": "2024-07-09T10:30:00Z"
}
```

#### Not Found Error (404)
```json
{
    "status": "error",
    "status_code": 404,
    "message": "Task not found",
    "data": null,
    "timestamp": "2024-07-09T10:30:00Z"
}
```

## Configuration

### Swagger Configuration
The Swagger documentation is configured in `config/l5-swagger.php`:

```php
'defaults' => [
    'routes' => [
        'api' => 'api/documentation',
    ],
    'paths' => [
        'docs' => storage_path('api-docs'),
        'docs_json' => 'api-docs.json',
        'annotations' => [
            base_path('app/Http/Controllers/Api'),
        ],
    ],
],
```

### Generating Documentation
To regenerate the documentation after making changes:

```bash
# Generate Swagger documentation
php artisan l5-swagger:generate

# Generate for specific API (if multiple APIs configured)
php artisan l5-swagger:generate default

# Clear existing documentation cache
php artisan l5-swagger:generate --clear

# Generate with verbose output
php artisan l5-swagger:generate --verbose
```

### Complete Setup Commands
Here are all the commands needed to set up and manage Swagger documentation:

#### Initial Setup (if not already done)
```bash
# Install L5-Swagger package
composer require "darkaonline/l5-swagger"

# Publish configuration file
php artisan vendor:publish --provider "L5Swagger\L5SwaggerServiceProvider"

# Clear config cache (if needed)
php artisan config:clear
```

#### Generate Documentation
```bash
# Standard generation
php artisan l5-swagger:generate

# Force regeneration (ignores cache)
php artisan l5-swagger:generate --force

# Generate with detailed output
php artisan l5-swagger:generate -v
```

#### Development Commands
```bash
# Clear all Laravel caches
php artisan optimize:clear

# Clear specific caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear

# Generate documentation after clearing caches
php artisan l5-swagger:generate
```

#### Troubleshooting Commands
```bash
# Check if routes are loaded
php artisan route:list | findstr api

# Verify configuration
php artisan config:show l5-swagger

# Check for annotation errors
php artisan l5-swagger:generate --verbose 2>&1

# Clear and regenerate everything
php artisan optimize:clear && php artisan l5-swagger:generate
```

## Quick Command Reference

### Essential Swagger Commands
```bash
# Generate Swagger documentation
php artisan l5-swagger:generate

# Access documentation
# Open browser: http://localhost:8000/api/documentation

# Check API routes
php artisan route:list | findstr api

# Start development server
php artisan serve
```

### PowerShell Commands (Windows)
```powershell
# Generate documentation
php artisan l5-swagger:generate

# Filter API routes in PowerShell
php artisan route:list | Select-String "api"

# Clear all caches and regenerate
php artisan optimize:clear; php artisan l5-swagger:generate

# Start server and open documentation
Start-Process "http://localhost:8000/api/documentation"
php artisan serve
```

### Batch Generation Script
Create a `generate-docs.bat` file for easy regeneration:
```batch
@echo off
echo Clearing caches...
php artisan config:clear
php artisan cache:clear

echo Generating Swagger documentation...
php artisan l5-swagger:generate

echo Documentation generated successfully!
echo Access at: http://localhost:8000/api/documentation
pause
```

## Development Notes

### Adding New Endpoints
When adding new API endpoints:

1. Add appropriate Swagger annotations to the controller method
2. Follow the existing annotation patterns
3. Include all parameters, request/response schemas
4. Regenerate documentation with `php artisan l5-swagger:generate`

### Annotation Structure
```php
/**
 * @OA\Get(
 *     path="/api/endpoint",
 *     operationId="uniqueOperationId",
 *     tags={"Tags"},
 *     summary="Brief summary",
 *     description="Detailed description",
 *     @OA\Parameter(...),
 *     @OA\Response(
 *         response=200,
 *         description="Success response",
 *         @OA\JsonContent(ref="#/components/schemas/SchemaName")
 *     )
 * )
 */
```

### Schema Definitions
Global schemas are defined in `app/Http/Controllers/Api/SwaggerAnnotations.php`:
- `ApiResponse` - Standard API response format
- `Task` - Task entity schema
- `PaginatedTasks` - Paginated task response
- `ValidationError` - Validation error response
- `TaskStatistics` - Task statistics schema

## Testing with Swagger UI

1. **Navigate** to the documentation URL
2. **Explore** available endpoints in the interface
3. **Expand** an endpoint to see details
4. **Click** "Try it out" button
5. **Fill** in required parameters
6. **Execute** the request
7. **Review** the response

## Best Practices

### API Usage
- Always include `Accept: application/json` header
- Use appropriate HTTP methods (GET, POST, PUT, DELETE)
- Handle pagination for list endpoints
- Implement proper error handling
- Validate input data on client side

### Documentation
- Keep annotations up to date with code changes
- Provide clear descriptions and examples
- Include all possible response codes
- Document all parameters and their constraints
- Use consistent naming conventions

## Support

For issues with the API or documentation:
1. Check this documentation first
2. Verify your request format matches the examples
3. Check the application logs for detailed error messages
4. Ensure all required parameters are provided
5. Validate your JSON request body format

## Version Information

- **API Version**: 1.0
- **OpenAPI Version**: 3.0.0
- **Laravel Version**: 11.x
- **L5-Swagger Version**: Latest

---

*This documentation is automatically generated from code annotations and should be kept in sync with the actual API implementation.*
