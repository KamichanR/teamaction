<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectRole;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class ProjectController extends Controller
{
    public function getCreateData()
    {
        $areas = Area::all();
        $skills = Skill::all();
        $projectCategories = ProjectCategory::all();

        $data = [
            'areas' => $areas,
            'skills' => $skills,
            'projectCategories' => $projectCategories,
        ];

        return response($data, Response::HTTP_OK);
    }

    public function createProject(Request $request)
    {
        $project = Project::create([
            'user_id' => $request->userId,
            'project_title' => $request->projectTitle,
            'project_image_uri' => $request->projectImageUri,
            'area_id' => $request->area['id'],
            'project_category_id' => $request->projectCategory['id'],
            'purpose' => $request->purpose,
            'description' => $request->description,
            'ideal_candidate' => $request->idealCandidate,
            'merit' => $request->merit,
        ]);

        foreach ($request->skills as $skill) {
            DB::table('project_skill')
                ->insert([
                    'project_id' => $project->id,
                    'skill_id' => $skill['id']
                ]);
        }

        foreach ($request->projectRoles as $projectRole) {
            ProjectRole::create([
                'project_id' => $project->id,
                'role_title' => $projectRole['role_title'],
                'number_of_applicants' => $projectRole['number_of_applicants'],
                'close' => $projectRole['close'],
                'description' => $projectRole['description'],
            ]);
        }

        return response($project->id, Response::HTTP_OK);
    }

    public function readProject($projectId)
    {
        $data = Project::find($projectId);

        if (!$data) return response(null, Response::HTTP_NO_CONTENT);

        $area = $data->area()->first();
        $projectCategory = $data->projectCategory()->first();
        $skills = $data->skills()->get();
        $projectRoles = $data->projectRoles()->get();
        $user = $data->user()->first();

        $project = [
            'id' => $data->id,
            'userId' => $data->user_id,
            'projectTitle' => $data->project_title,
            'projectImageUri' => $data->project_image_uri,
            'area' => $area,
            'projectCategory' => $projectCategory,
            'skills' => $skills,
            'purpose' => $data->purpose,
            'description' => $data->description,
            'idealCandidate' => $data->ideal_candidate,
            'merit' => $data->merit,
            'projectRoles' => $projectRoles,
            'username' => $user->username,
            'usericonImageUri' => $user->usericon_image_uri,
        ];

        // ??????????????????????????????????????????????????????????????????????????????????????????
        if (Auth::check()) {
            $favorite = DB::table('favorites')
                        ->where('user_id', Auth::id())
                        ->where('project_id', $data->id)
                        ->exists();
            return response()->json(['project' => $project, 'favorite' => $favorite], Response::HTTP_OK);
        }

        return response()->json(['project' => $project], Response::HTTP_OK);
    }

    public function updateProject(Request $request)
    {
        $project = Project::find($request->id);

        $project->update([
            'project_title' => $request->projectTitle,
            'project_image_uri' => $request->projectImageUri,
            'area_id' => $request->area['id'],
            'project_category_id' => $request->projectCategory['id'],
            'purpose' => $request->purpose,
            'description' => $request->description,
            'ideal_candidate' => $request->idealCandidate,
            'merit' => $request->merit,
        ]);

        DB::table('project_skill')
            ->where('user_id', $request->userId)
            ->delete();

        foreach ($request->skills as $skill) {
            DB::table('project_skill')
                ->insert([
                    'user_id' => $request->userId,
                    'skill_id' => $skill['id'],
                ]);
        }

        ProjectRole::where('project_id', $project->id)
            ->delete();

        foreach ($request->projectRoles as $role) {
            ProjectRole::create([
                'project_id' => $role->id,
                'role_title' => $role->role_title,
                'number_of_applicants' => $role->number_of_applicants,
                'description' => $role->description,
            ]);
        }

        return response(null, Response::HTTP_OK);
    }

    public function deleteProject($projectId)
    {
        $project = Project::find($projectId);

        if (!$project) return response(null, Response::HTTP_NO_CONTENT);

        $project->delete();

        return response(null, Response::HTTP_OK);
    }

    public function getProjectsData($group, Request $request)
    {
        $skill = (int) $request->skill;
        $projectCategory = (int) $request->projectCategory;
        $area = (int) $request->area;

        // ??????????????????????????????????????????
        if ($skill !== 0) {
            $projects = Project::whereHas('skills', function($q) use($skill) {
                $q->where('id', $skill);
            });
        } else {
            $projects = Project::all();
        }

        if ($projectCategory !== 0) $projects = $projects->where('project_category_id', $projectCategory);
        if ($area !== 0) $projects = $projects->where('area_id', $area);

        // ??????????????????12???????????????
        $projects = $projects->skip(($group - 1) * 12)
                        ->take(12);
        if ($skill !== 0) $projects = $projects->get();

        foreach ($projects as $project) {
            $project->user = $project->user()->first();
        }

        $count = Project::count();

        $areas = Area::all();
        $skills = Skill::all();
        $projectCategories = ProjectCategory::all();

        return response()->json([
            'projects' => $projects,
            'count' => $count,
            'areas' => $areas,
            'skills' => $skills,
            'projectCategories' => $projectCategories,
        ], Response::HTTP_OK);
    }

    public function updateFavorite($projectId, Request $request)
    {
        // ?????????????????????????????????
        $exist = DB::table('favorites')
                    ->where([
                        'user_id' => Auth::id(),
                        'project_id' => (int) $projectId,
                    ])
                    ->exists();

        if ($request->favorite && !$exist) {
            DB::table('favorites')
                ->insert([
                    'user_id' => Auth::id(),
                    'project_id' => (int) $projectId,
                ]);
        } else if (!$request->favorite && $exist) {
            DB::table('favorites')
                ->where([
                    'user_id' => Auth::id(),
                    'project_id' => (int) $projectId,
                ])
                ->delete();
        }

        return response(null, Response::HTTP_OK);
    }
}
