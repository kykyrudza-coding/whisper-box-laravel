<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Resources\JsonApi\JsonApiResource;

class NoteResource extends JsonApiResource
{
    public array $attributes = [
        'title',
        'content',
        'mood',
        'created_at',
        'likes_count'
    ];
}
