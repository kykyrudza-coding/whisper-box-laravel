<?php

declare(strict_types=1);

use App\Http\Controllers\NoteController;
use Illuminate\Support\Facades\Route;

Route::apiResource('notes', NoteController::class)
    ->except('destroy');

Route::post('notes/{note}/like', [NoteController::class, 'like']);
