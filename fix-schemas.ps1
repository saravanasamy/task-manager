# Fix Swagger schema references
$filePath = "app\Http\Controllers\Api\TaskController.php"
$content = Get-Content $filePath -Raw

# Replace Task schema references
$content = $content -replace 'ref="#/components/schemas/Task"', 'type="object", @OA\Property(property="id", type="integer", example=1), @OA\Property(property="title", type="string", example="Sample Task"), @OA\Property(property="description", type="string", example="Task description"), @OA\Property(property="status", type="string", example="pending"), @OA\Property(property="due_date", type="string", format="date", example="2024-07-15"), @OA\Property(property="created_at", type="string", format="date-time"), @OA\Property(property="updated_at", type="string", format="date-time")'

# Replace ValidationError schema references
$content = $content -replace 'ref="#/components/schemas/ValidationError"', 'type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=422), @OA\Property(property="message", type="string", example="Validation failed"), @OA\Property(property="validation_errors", type="object"), @OA\Property(property="timestamp", type="string", format="date-time")'

# Replace ErrorResponse schema references
$content = $content -replace 'ref="#/components/schemas/ErrorResponse"', 'type="object", @OA\Property(property="status", type="string", example="error"), @OA\Property(property="status_code", type="integer", example=404), @OA\Property(property="message", type="string", example="Resource not found"), @OA\Property(property="timestamp", type="string", format="date-time")'

# Replace TaskStatistics schema references
$content = $content -replace 'ref="#/components/schemas/TaskStatistics"', 'type="object", @OA\Property(property="total", type="integer", example=47), @OA\Property(property="pending", type="integer", example=15), @OA\Property(property="in_progress", type="integer", example=12), @OA\Property(property="completed", type="integer", example=20), @OA\Property(property="overdue", type="integer", example=5)'

Set-Content $filePath $content

Write-Host "Schema references fixed!"
