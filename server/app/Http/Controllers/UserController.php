<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Skill;
use App\Models\User;
use App\Models\Workstyle;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function linkWithProvider($provider)
    {
        try {
            $userId = Auth::id();
            $user = User::find($userId);

            switch ($provider) {
                case 'twitter':
                    $user->update(['linked_with_twitter' => true]);
                    break;
                case 'google':
                    $user->update(['linked_with_google' => true]);
                    break;
                default:
                    return response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
            }

            return response(null, Response::HTTP_OK);
        } catch (Exception $e) {
            return response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function getUserData($userId)
    {
        $data = User::find($userId);

        if (!$data) return response(null, Response::HTTP_NO_CONTENT);

        $organizations = $data->organizations()->get();
        $area = $data->area()->first();
        $workstyles = $data->workstyles()->get();
        $skills = $data->skills()->get();
        $careers = $data->careers()->get();
        $certifications = $data->certifications()->get();

        $tmp = [
            'userId' => $data->id,
            'username' => $data->username,
            'linkedWithTwitter' => $data->linked_with_twitter,
            'linkedWithGoogle' => $data->linked_with_google,
            'twitterUri' => $data->twitter_uri,
            'facebookUri' => $data->facebook_uri,
            'websiteUri' => $data->website_uri,
            'usericonImageUri' => $data->usericon_image_uri,
            'area' => $area,
            'introduction' => $data->introduction,
            'wannaDo' => $data->wanna_do,
            'organizations' => $organizations,
            'workstyles' => $workstyles,
            'skills' => $skills,
            'careers' => $careers,
            'certifications' => $certifications,
        ];

        $user = json_encode($tmp);

        return response($user, Response::HTTP_OK);
    }

    public function getEditData()
    {
        $areas = Area::all();
        $skills = Skill::all();
        $workstyles = Workstyle::all();

        $data = [
            'areas' => $areas,
            'skills' => $skills,
            'workstyles' => $workstyles,
        ];

        return response($data, Response::HTTP_OK);
    }

    public function updateUser(Request $request)
    {
        $user = User::find($request->userId);

        $user->update([
            'username' => $request->username,
            'twitter_uri' => $request->twitterUri,
            'facebook_uri' => $request->facecbookUri,
            'website_uri' => $request->websiteUri,
            'usericon_image_uri' => $request->usericonImageUri,
            'area_id' => $request->area['id'] ?? null,
            'introduction' => $request->introduction,
            'wanna_do' => $request->wannaDo,
        ]);

        DB::table('user_workstyle')
            ->where('user_id', $request->userId)
            ->delete();

        if ($request->workstyles) {
            foreach ($request->workstyles as $workstyle) {
                DB::table('user_workstyle')
                    ->insert([
                        'user_id' => $request->userId,
                        'workstyle_id' => $workstyle['id'],
                    ]);
            }
        }

        DB::table('skill_user')
            ->where('user_id', $request->userId)
            ->delete();

        if ($request->skills) {
            foreach ($request->skills as $skill) {
                DB::table('skill_user')
                    ->insert([
                        'user_id' => $request->userId,
                        'skill_id' => $skill['id'],
                    ]);
            }
        }

        DB::table('organizations')
            ->where('user_id', $request->userId)
            ->delete();

        if ($request->organizations) {
            foreach ($request->organizations as $organization) {
                DB::table('organizations')
                    ->insert([
                        'user_id' => $request->userId,
                        'name' => $organization['name'],
                    ]);
            }
        }

        DB::table('careers')
            ->where('user_id', $request->userId)
            ->delete();

        if ($request->careers) {
            foreach ($request->careers as $career) {
                DB::table('careers')
                    ->insert([
                        'user_id' => $request->userId,
                        'begin' => $career['begin'],
                        'end' => $career['end'],
                        'company' => $career['company'],
                        'position' => $career['position'],
                        'description' => $career['description'],
                    ]);
            }
        }

        DB::table('certifications')
            ->where('user_id', $request->userId)
            ->delete();

        if ($request->certifications) {
            foreach ($request->certifications as $certification) {
                DB::table('certifications')
                    ->insert([
                        'user_id' => $request->userId,
                        'name' => $certification['name'],
                        'acquisition' => $certification['acquisition'],
                    ]);
            }
        }

        return response(null, Response::HTTP_OK);
    }

    public function getUsersData($group)
    {
        $users = User::offset(($group - 1) * 16)
                    ->limit(16)
                    ->get();

        foreach ($users as $user) {
            $user->skills = $user->skills()->get();
        }

        $count = User::count();

        return response()->json(['users' => $users, 'count' => $count], Response::HTTP_OK);
    }
}
