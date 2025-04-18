<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ErrorResource extends JsonResource
{
    public static $wrap = 'error';

    public function toArray($request)
    {
        return [
            'status' => 'error',
            'code' => $this->resource['code'] ?? 400,
            'message' => $this->resource['message'] ?? 'An error occurred',
            'details' => $this->resource['details'] ?? null,
        ];
    }

    public function withResponse($request, $response)
    {
        $response->setStatusCode($this->resource['code'] ?? 400);
    }
}