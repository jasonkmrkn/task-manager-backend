<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; 

class ProjectController extends Controller
{
    /**
     * Display a listing of the projects for the logged-in user.
     */
    public function index(Request $request)
    {
        $user = $request->user();

        return response()->json($user->projects);
    }

    /**
     * Store a newly created project in the database.
     */
    public function store(Request $request)
    {
        // 1. validasi data masuk
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // 2. get user yg udah authenticated
        $user = $request->user();

        // 3. create project baru dari user itu
        $project = $user->projects()->create([
            'name' => $validated['name'],
        ]);

        // 4. return project yg baru dibuat dalam json response
        return response()->json($project, 201);
    }

    /**
     * Display the specified project.
     */
    public function show(Request $request, Project $project)
    {
        // 1. cek apakah user yg authenticated punya project ini
        if ($request->user()->id !== $project->user_id) {
            // kalo ga, return 403 forbidden response
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        // 2. kalo iya, return projectnya dalam json response
        return response()->json($project);
    }

    /**
     * Update the specified project in the database.
     */
    public function update(Request $request, Project $project)
    {
        // 1. cek apakah user yg authenticated punya project ini
        if ($request->user()->id !== $project->user_id) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        // 2. validasi data masuk
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // 3. update projectnya
        $project->update([
            'name' => $validated['name'],
        ]);

        // 4. return dalam json response
        return response()->json($project);
    }

    /**
     * Remove the specified project from the database.
     */
    public function destroy(Request $request, Project $project)
    {
        // 1. cek apakah user yg authenticated punya project ini
        if ($request->user()->id !== $project->user_id) {
            return response()->json(['message' => 'This action is unauthorized.'], 403);
        }

        // 2. delete projectnya
        $project->delete();

        // 3. return 204 no content response (standard for successful deletes)
        return response()->json(null, 204);
    }
}