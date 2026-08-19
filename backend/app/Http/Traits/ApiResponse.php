<?php

namespace App\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Return a standardized success JSON response for a single item.
     */
    protected function successResponse(mixed $data = [], string $message = 'Request successful', int $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    /**
     * Return a standardized collection JSON response.
     */
    protected function collectionResponse(mixed $data = [], string $message = 'Data retrieved successfully', array $meta = [], int $code = 200): JsonResponse
    {
        $response = [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];

        if (! empty($meta)) {
            $response['meta'] = $meta;
        }

        return response()->json($response, $code);
    }

    /**
     * Return a standardized paginated collection JSON response.
     */
    protected function paginatedResponse(mixed $resource, string $message = 'Data retrieved successfully', int $code = 200): JsonResponse
    {
        $paginator = null;

        if ($resource instanceof AnonymousResourceCollection && $resource->resource instanceof LengthAwarePaginator) {
            $paginator = $resource->resource;
        } elseif ($resource instanceof LengthAwarePaginator) {
            $paginator = $resource;
        }

        $meta = $paginator ? [
            'current_page' => $paginator->currentPage(),
            'last_page' => $paginator->lastPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'from' => $paginator->firstItem(),
            'to' => $paginator->lastItem(),
        ] : [];

        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $resource,
            'meta' => $meta,
        ], $code);
    }

    /**
     * Return a standardized error JSON response.
     */
    protected function errorResponse(string $message = 'Request failed', int $code = 400, mixed $errors = null): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $message,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return response()->json($response, $code);
    }
}
