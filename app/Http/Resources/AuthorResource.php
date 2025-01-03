<?php

namespace App\Http\Resources;

use App\JsonApi\Traits\JsonApiResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    use JsonApiResource;

    public function toJsonApi(): array
    {
        return [
            'name' => $this->resource->name,
        ];
    }
}
