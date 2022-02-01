<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Project;
use App\Models\ProjectCategory;
use App\Models\ProjectRole;
use App\Models\Skill;
use Illuminate\Http\Request;
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
                'role_title' => $projectRole['roleTitle'],
                'number_of_applicants' => $projectRole['numberOfApplicants'],
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

        $tmp = [
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

        $project = json_encode($tmp);

        return response($project, Response::HTTP_OK);
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
}
