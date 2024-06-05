<?php

namespace App\Http\Controllers;

use App\Http\Resources\HostResource;
use App\Http\Resources\TaskResource;
use App\Services\TaskService;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $taskService = TaskService::make();
        return response()->json(['completed' => $taskService->getNumberOfCompleted()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'raw_data' => 'string|nullable',
            'hosts' => 'required|array'
        ]);

        $taskService = TaskService::make();
        $task = $taskService->saveTaskWithHosts($validated['hosts']);

        $taskService->startCheckingHosts($task);

        return (new TaskResource($task->hosts));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }
}
