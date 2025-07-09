<?php

namespace App\Http\Controllers\Api;

/**
 * @OA\Info(
 *     title="Task Manager API",
 *     version="1.0.0",
 *     description="A comprehensive task management API built with Laravel. This API provides full CRUD operations for task management with advanced filtering, sorting, and bulk operations.",
 *     @OA\Contact(
 *         email="developer@taskmanager.com",
 *         name="API Support"
 *     ),
 *     @OA\License(
 *         name="MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Task Manager API Server"
 * )
 * 
 * @OA\Tag(
 *     name="Tasks",
 *     description="Task management operations"
 * )
 * 
 * @OA\Tag(
 *     name="Statistics",
 *     description="Task statistics and analytics"
 * )
 * 
 * @OA\Tag(
 *     name="Health",
 *     description="API health check endpoints"
 * )
 */

class SwaggerAnnotations
{
    // This class exists solely to hold Swagger annotations
}

/**
 * @OA\Schema(
 *     schema="Task",
 *     type="object",
 *     title="Task",
 *     description="Task model",
 *     required={"id", "title", "status", "created_at", "updated_at"},
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="Task ID",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Task title",
 *         maxLength=255,
 *         example="Complete project documentation"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Task description",
 *         nullable=true,
 *         maxLength=2000,
 *         example="Write comprehensive documentation for the task management system"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "in_progress", "completed"},
 *         description="Task status",
 *         example="pending"
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date",
 *         description="Task due date",
 *         nullable=true,
 *         example="2024-12-31"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Creation timestamp",
 *         example="2024-07-09T10:30:00.000000Z"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Last update timestamp",
 *         example="2024-07-09T15:45:00.000000Z"
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="TaskInput",
 *     type="object",
 *     title="Task Input",
 *     description="Task input for creation/update",
 *     required={"title", "status"},
 *     @OA\Property(
 *         property="title",
 *         type="string",
 *         description="Task title",
 *         minLength=3,
 *         maxLength=255,
 *         example="Complete project documentation"
 *     ),
 *     @OA\Property(
 *         property="description",
 *         type="string",
 *         description="Task description",
 *         nullable=true,
 *         maxLength=2000,
 *         example="Write comprehensive documentation for the task management system"
 *     ),
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         enum={"pending", "in_progress", "completed"},
 *         description="Task status",
 *         example="pending"
 *     ),
 *     @OA\Property(
 *         property="due_date",
 *         type="string",
 *         format="date",
 *         description="Task due date",
 *         nullable=true,
 *         example="2024-12-31"
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="ApiResponse",
 *     type="object",
 *     title="API Response",
 *     description="Standard API response format",
 *     required={"status", "status_code", "message", "timestamp"},
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         description="Response status",
 *         example="success"
 *     ),
 *     @OA\Property(
 *         property="status_code",
 *         type="integer",
 *         description="HTTP status code",
 *         example=200
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Response message",
 *         example="Tasks retrieved successfully"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         description="Response data",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="validation_errors",
 *         type="object",
 *         description="Validation errors",
 *         nullable=true
 *     ),
 *     @OA\Property(
 *         property="timestamp",
 *         type="string",
 *         format="date-time",
 *         description="Response timestamp",
 *         example="2024-07-09T10:30:00.000000Z"
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="PaginatedTasks",
 *     type="object",
 *     title="Paginated Tasks",
 *     description="Paginated task response",
 *     required={"current_page", "data", "first_page_url", "from", "last_page", "last_page_url", "path", "per_page", "to", "total"},
 *     @OA\Property(
 *         property="current_page",
 *         type="integer",
 *         description="Current page number",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="data",
 *         type="array",
 *         description="Array of tasks",
 *         @OA\Items(ref="#/components/schemas/Task")
 *     ),
 *     @OA\Property(
 *         property="first_page_url",
 *         type="string",
 *         description="URL of the first page",
 *         example="http://localhost:8000/api/tasks?page=1"
 *     ),
 *     @OA\Property(
 *         property="from",
 *         type="integer",
 *         description="First item number on current page",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="last_page",
 *         type="integer",
 *         description="Last page number",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="last_page_url",
 *         type="string",
 *         description="URL of the last page",
 *         example="http://localhost:8000/api/tasks?page=5"
 *     ),
 *     @OA\Property(
 *         property="links",
 *         type="array",
 *         description="Pagination links",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="url", type="string", nullable=true),
 *             @OA\Property(property="label", type="string"),
 *             @OA\Property(property="active", type="boolean")
 *         )
 *     ),
 *     @OA\Property(
 *         property="next_page_url",
 *         type="string",
 *         nullable=true,
 *         description="URL of the next page",
 *         example="http://localhost:8000/api/tasks?page=2"
 *     ),
 *     @OA\Property(
 *         property="path",
 *         type="string",
 *         description="Base path for pagination",
 *         example="http://localhost:8000/api/tasks"
 *     ),
 *     @OA\Property(
 *         property="per_page",
 *         type="integer",
 *         description="Number of items per page",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="prev_page_url",
 *         type="string",
 *         nullable=true,
 *         description="URL of the previous page",
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="to",
 *         type="integer",
 *         description="Last item number on current page",
 *         example=10
 *     ),
 *     @OA\Property(
 *         property="total",
 *         type="integer",
 *         description="Total number of items",
 *         example=45
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="TaskStatistics",
 *     type="object",
 *     title="Task Statistics",
 *     description="Task statistics overview",
 *     @OA\Property(
 *         property="total",
 *         type="integer",
 *         description="Total number of tasks",
 *         example=47
 *     ),
 *     @OA\Property(
 *         property="pending",
 *         type="integer",
 *         description="Number of pending tasks",
 *         example=15
 *     ),
 *     @OA\Property(
 *         property="in_progress",
 *         type="integer",
 *         description="Number of in progress tasks",
 *         example=12
 *     ),
 *     @OA\Property(
 *         property="completed",
 *         type="integer",
 *         description="Number of completed tasks",
 *         example=20
 *     ),
 *     @OA\Property(
 *         property="overdue",
 *         type="integer",
 *         description="Number of overdue tasks",
 *         example=5
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="ValidationError",
 *     type="object",
 *     title="Validation Error",
 *     description="Validation error response",
 *     required={"status", "status_code", "message", "timestamp"},
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         example="error"
 *     ),
 *     @OA\Property(
 *         property="status_code",
 *         type="integer",
 *         example=422
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Validation failed"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         nullable=true,
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="validation_errors",
 *         type="object",
 *         example={
 *             "title": {"The title field is required."},
 *             "status": {"The status field is required."}
 *         }
 *     ),
 *     @OA\Property(
 *         property="timestamp",
 *         type="string",
 *         format="date-time",
 *         example="2024-07-09T10:30:00.000000Z"
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="BulkActionRequest",
 *     type="object",
 *     title="Bulk Action Request",
 *     description="Request for bulk operations",
 *     required={"action", "task_ids"},
 *     @OA\Property(
 *         property="action",
 *         type="string",
 *         enum={"delete", "mark_completed", "mark_pending", "mark_in_progress"},
 *         description="Action to perform",
 *         example="mark_completed"
 *     ),
 *     @OA\Property(
 *         property="task_ids",
 *         type="array",
 *         @OA\Items(type="integer"),
 *         description="Array of task IDs",
 *         example={1, 2, 3}
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     type="object",
 *     title="Error Response",
 *     description="Standard error response format",
 *     required={"status", "status_code", "message", "timestamp"},
 *     @OA\Property(
 *         property="status",
 *         type="string",
 *         example="error"
 *     ),
 *     @OA\Property(
 *         property="status_code",
 *         type="integer",
 *         example=404
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         example="Resource not found"
 *     ),
 *     @OA\Property(
 *         property="data",
 *         nullable=true,
 *         example=null
 *     ),
 *     @OA\Property(
 *         property="timestamp",
 *         type="string",
 *         format="date-time",
 *         example="2024-07-09T10:30:00.000000Z"
 *     )
 * )
 */

/**
 * @OA\Schema(
 *     schema="BulkOperationResponse",
 *     type="object",
 *     title="Bulk Operation Response",
 *     description="Response for bulk operations",
 *     @OA\Property(
 *         property="processed",
 *         type="integer",
 *         description="Number of items processed successfully",
 *         example=3
 *     ),
 *     @OA\Property(
 *         property="failed",
 *         type="integer",
 *         description="Number of items that failed to process",
 *         example=0
 *     ),
 *     @OA\Property(
 *         property="message",
 *         type="string",
 *         description="Operation result message",
 *         example="3 tasks updated successfully"
 *     ),
 *     @OA\Property(
 *         property="errors",
 *         type="array",
 *         @OA\Items(type="string"),
 *         description="Array of error messages for failed items",
 *         example={}
 *     )
 * )
 */
