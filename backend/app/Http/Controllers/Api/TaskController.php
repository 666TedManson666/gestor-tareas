<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Models\Task;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = Task::with('items')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json(['data' => $tasks]);
    }

    public function store(StoreTaskRequest $request): JsonResponse
    {
        $task = Task::create($request->validated());

        return response()->json(
            ['data' => $task->load('items'), 'message' => 'Tarea creada correctamente.'],
            201
        );
    }

    public function update(UpdateTaskRequest $request, Task $task): JsonResponse
    {
        $validated = $request->validated();

        if (isset($validated['status']) && $validated['status'] === 'done') {
            $task->load('items');

            if (!$task->canBeMarkedAsDone()) {
                return response()->json([
                    'message' => 'No se puede marcar la tarea como terminada porque tiene ítems incompletos.',
                    'errors'  => [
                        'status' => ['Todos los ítems deben estar completados antes de marcar la tarea como "done".'],
                    ],
                ], 422);
            }
        }

        $task->update($validated);

        return response()->json([
            'data'    => $task->load('items'),
            'message' => 'Tarea actualizada correctamente.',
        ]);
    }

    public function destroy(Task $task): JsonResponse
    {
        $task->delete();

        return response()->json(['message' => 'Tarea eliminada correctamente.']);
    }
}
