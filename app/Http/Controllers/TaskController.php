<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->tasks();

        // Feature #8: Filtering Logic

        // Check if a 'search' parameter exists
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->input('search') . '%');
        }

        // Check for a 'project_id' filter
        if ($request->has('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        // Check for a 'status_id' filter
        if ($request->has('status_id')) {
            $query->where('status_id', $request->input('status_id'));
        }

        // Check for a 'priority_id' filter
        if ($request->has('priority_id')) {
            $query->where('priority_id', $request->input('priority_id'));
        }
        
        // Include project, status, and priority details
        $tasks = $query->with(['project', 'status', 'priority'])->get();

        return response()->json($tasks);
    }

    /**
     * Store a newly created task
     * Feature #1: Adding a Task
     * Feature #2: Adding a Due Date
     * Feature #3: Setting a Priority
     */
    public function store(Request $request)
    {
        // 1. Validate all incoming data
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'required|integer|exists:projects,id',
            'status_id' => 'required|integer|exists:status,id',
            'priority_id' => 'nullable|integer|exists:priority,id',
            'due_date' => 'nullable|date',
        ]);

        // 2. Verify the project belongs to the authenticated user
        $project = Project::find($validated['project_id']);
        if ($request->user()->id !== $project->user_id) {
            return response()->json(['message' => 'This project does not belong to you.'], 403);
        }

        // 3. Create the task
        $task = Task::create($validated);

        // 4. Return the new task with its relationships
        return response()->json($task->load(['project', 'status', 'priority']), 201);
    }

    /**
     * Display the specified task.
     */
    public function show(Request $request, Task $task)
    {
        // 1. Ensure the user owns this task
        if ($request->user()->id !== $task->project->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Return the task with its relationships
        return response()->json($task->load(['project', 'status', 'priority']));
    }

    /**
     * Feature #4: Modifying a Task (title, due_date, priority, etc.)
     * Feature #5: Moving a Task (changing its 'status_id')
     */
    public function update(Request $request, Task $task)
    {
        // 1. Ensure the user owns this task
        if ($request->user()->id !== $task->project->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Validate the incoming data
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'project_id' => 'sometimes|integer|exists:projects,id',
            'status_id' => 'sometimes|integer|exists:status,id',
            'priority_id' => 'nullable|integer|exists:priority,id',
            'due_date' => 'nullable|date',
        ]);

        // 3. Deeper Security Check:
        // If the user is trying to change the project_id, we must check if they own the new project as well.
        if ($request->has('project_id') && $validated['project_id'] !== $task->project_id) {
            $newProject = Project::find($validated['project_id']);
            if ($request->user()->id !== $newProject->user_id) {
                return response()->json(['message' => 'You cannot move this task to a project you do not own.'], 403);
            }
        }

        // 4. Update the task
        $task->update($validated);

        // 5. Return the updated task with its relationships
        return response()->json($task->load(['project', 'status', 'priority']));
    }

    /**
     * Feature #6: Removing a Task
     */
    public function destroy(Request $request, Task $task)
    {
        // 1. Ensure the user owns this task
        if ($request->user()->id !== $task->project->user_id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // 2. Delete the task
        $task->delete();

        // 3. Return a "No Content" response, standard for a successful DELETE
        return response()->json(null, 204);
    }
}

