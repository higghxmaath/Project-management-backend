<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\ProjectMember;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Services\ActivityLogger;
use App\Services\ProjectAuthorizationService;

class ProjectController extends Controller
{   
      public function index(Request $request)
{
    $user = auth('api')->user();
    if (! $user) {
        return response()->json(['message' => 'Unauthenticated'], 401);
    }

    $perPage = min(
        $request->get('per_page', config('pagination.default_per_page')),
        config('pagination.max_per_page')
    );

    $projects = Project::whereHas('members', function ($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->with(['members:id,user_id,role'])
        ->latest()
        ->paginate($perPage);

    return response()->json($projects);
}


    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $user = auth('api')->user();

        $project = Project::create([
            'name'        => $data['name'],
            'description' => $data['description'] ?? null,
            'owner_id'    => $user->id,
        ]);

        ProjectMember::create([
            'project_id' => $project->id,
            'user_id'    => $user->id,
            'role'       => 'owner',
        ]);

        ActivityLogger::log(
            'project.created',
            null,
            ['project_id' => $project->id, 'name' => $project->name]
        );

        return response()->json($project, Response::HTTP_CREATED);
    }

    
    public function show(string $projectId)
{
    $project = Project::with('members')
        ->findOrFail($projectId);

    ProjectAuthorizationService::check($project, 'viewer');

    return response()->json($project);
}

public function destroy(Project $project)
{
    $project->delete();

    return response()->json([
        'message' => 'Project deleted'
    ]);
}


}