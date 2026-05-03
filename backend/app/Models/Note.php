<?php

namespace App\Models;

use App\Enums\MoodEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'mood',
    ];

    protected $casts = [
        'mood' => MoodEnum::class,
    ];
}
