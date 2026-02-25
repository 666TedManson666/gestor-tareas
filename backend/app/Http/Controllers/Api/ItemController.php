<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use App\Models\Item;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class ItemController extends Controller
{
    public function store(StoreItemRequest $request, Task $task): JsonResponse
    {
        $item = $task->items()->create($request->validated());

        return response()->json(
            ['data' => $item, 'message' => 'Ítem creado correctamente.'],
            201
        );
    }

    public function update(UpdateItemRequest $request, Task $task, Item $item): JsonResponse
    {
        $item->update($request->validated());

        return response()->json([
            'data'    => $item->fresh(),
            'message' => 'Ítem actualizado correctamente.',
        ]);
    }

    public function destroy(Task $task, Item $item): JsonResponse
    {
        $item->delete();

        return response()->json(['message' => 'Ítem eliminado correctamente.']);
    }
}
