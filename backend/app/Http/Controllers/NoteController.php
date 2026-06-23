<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\NoteRequest;
use App\Http\Resources\NoteResource;
use App\Models\Note;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class NoteController extends Controller
{
    public function index(): JsonResponse
    {
        $query = Note::query();

        if (request('sort') === 'popular') {
            $query->orderByDesc('likes_count');
        } else {
            $query->latest();
        }

        $notes = $query->get();

        return response()->json([
            'status' => 'success',
            'data' => NoteResource::collection($notes),
        ]);
    }

    public function show(Note $note): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'data' => new NoteResource($note),
        ]);
    }

    /**
     * @throws Throwable
     */
    public function store(NoteRequest $request): JsonResponse
    {
        $validated = $request->validated();

        DB::beginTransaction();

            $note = Note::query()
                ->create([
                    'title' => $validated['title'],
                    'content' => $validated['content'],
                    'mood' => $validated['mood'],
                ]);

        DB::commit();

        return response()->json([
            'status' => 'success',
            'message' => 'Note created successfully',
            'data' => new NoteResource($note),
        ]);
    }

    public function like(int $id): JsonResponse
    {
        $note = Note::query()->findOrFail($id);

        $note->query()->increment('likes_count');

        return response()->json([
            'status' => 'success',
            'data' => new NoteResource($note->refresh()),
        ]);
    }
}
