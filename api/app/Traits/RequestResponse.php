<?php

namespace App\Traits;

use Symfony\Component\HttpFoundation\Response;

trait RequestResponse
{
    public function success(string $message = "The operation completed successfully", mixed $data = null, int $statusCode = Response::HTTP_OK)
    {
        $response = [
            'response' => 'success',
            'status' => true,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    public function error(string $message, mixed $data = null, int $statusCode = Response::HTTP_BAD_REQUEST)
    {
        $response = [
            'response' => 'failed',
            'status' => false,
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        return response()->json($response, $statusCode);
    }

    public function notFound(string $message, mixed $data = null)
    {
        return $this->error($message, $data, Response::HTTP_NOT_FOUND);
    }

    public function internalServerError(string $message, mixed $data = null)
    {
        return $this->error($message, $data, Response::HTTP_INTERNAL_SERVER_ERROR);
    }

    public function unauthorized(string $message, mixed $data = null)
    {
        return $this->error($message, $data, Response::HTTP_UNAUTHORIZED);
    }

    public function forbidden(string $message, mixed $data = null)
    {
        return $this->error($message, $data, Response::HTTP_FORBIDDEN);
    }

    public function unprocessableEntity(string $message, mixed $data = null)
    {
        return $this->error($message, $data, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
}