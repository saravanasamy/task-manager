<?php

namespace App\Services;

use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ApiResponseService
{
    /**
     * Return a successful response
     */
    public static function success(
        mixed $data = null, 
        string $message = 'Success', 
        int $statusCode = Response::HTTP_OK
    ): JsonResponse {
        return response()->json([
            'status' => true,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'validation_errors' => null,
            'timestamp' => now()->toISOString()
        ], $statusCode);
    }

    /**
     * Return an error response
     */
    public static function error(
        string $message = 'Error occurred', 
        int $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR,
        mixed $data = null,
        array $validationErrors = null
    ): JsonResponse {
        return response()->json([
            'status' => false,
            'status_code' => $statusCode,
            'message' => $message,
            'data' => $data,
            'validation_errors' => $validationErrors,
            'timestamp' => now()->toISOString()
        ], $statusCode);
    }

    /**
     * Return a validation error response
     */
    public static function validationError(
        ValidationException $exception,
        string $message = 'Validation failed'
    ): JsonResponse {
        return self::error(
            message: $message,
            statusCode: Response::HTTP_UNPROCESSABLE_ENTITY,
            validationErrors: $exception->errors()
        );
    }

    /**
     * Return a not found response
     */
    public static function notFound(string $message = 'Resource not found'): JsonResponse {
        return self::error(
            message: $message,
            statusCode: Response::HTTP_NOT_FOUND
        );
    }

    /**
     * Return an unauthorized response
     */
    public static function unauthorized(string $message = 'Unauthorized'): JsonResponse {
        return self::error(
            message: $message,
            statusCode: Response::HTTP_UNAUTHORIZED
        );
    }

    /**
     * Return a forbidden response
     */
    public static function forbidden(string $message = 'Forbidden'): JsonResponse {
        return self::error(
            message: $message,
            statusCode: Response::HTTP_FORBIDDEN
        );
    }

    /**
     * Return a created response
     */
    public static function created(
        mixed $data = null, 
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return self::success(
            data: $data,
            message: $message,
            statusCode: Response::HTTP_CREATED
        );
    }

    /**
     * Return a paginated response
     */
    public static function paginated(
        $paginatedData,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return response()->json([
            'status' => true,
            'status_code' => Response::HTTP_OK,
            'message' => $message,
            'data' => $paginatedData->items(),
            'pagination' => [
                'current_page' => $paginatedData->currentPage(),
                'per_page' => $paginatedData->perPage(),
                'total' => $paginatedData->total(),
                'last_page' => $paginatedData->lastPage(),
                'from' => $paginatedData->firstItem(),
                'to' => $paginatedData->lastItem(),
                'has_more_pages' => $paginatedData->hasMorePages(),
                'links' => [
                    'first' => $paginatedData->url(1),
                    'last' => $paginatedData->url($paginatedData->lastPage()),
                    'prev' => $paginatedData->previousPageUrl(),
                    'next' => $paginatedData->nextPageUrl(),
                ]
            ],
            'validation_errors' => null,
            'timestamp' => now()->toISOString()
        ], Response::HTTP_OK);
    }

    /**
     * Return a collection response
     */
    public static function collection(
        $collection,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return self::success(
            data: [
                'items' => $collection,
                'count' => count($collection)
            ],
            message: $message
        );
    }

    /**
     * Return a bulk operation response
     */
    public static function bulkOperation(
        array $result,
        string $message = 'Bulk operation completed successfully'
    ): JsonResponse {
        return self::success(
            data: $result,
            message: $message
        );
    }

    /**
     * Handle generic exception and return appropriate response
     */
    public static function handleException(\Exception $exception): JsonResponse {
        // Log the exception for debugging
        \Log::error('API Exception: ' . $exception->getMessage(), [
            'exception' => $exception,
            'trace' => $exception->getTraceAsString()
        ]);

        if ($exception instanceof ValidationException) {
            return self::validationError($exception);
        }

        // Don't expose internal errors in production
        $message = app()->environment('production') 
            ? 'An error occurred while processing your request'
            : $exception->getMessage();

        return self::error(
            message: $message,
            statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
        );
    }
}
