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
}
