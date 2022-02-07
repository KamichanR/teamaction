<?php

namespace App\Http\Controllers;

use App\Models\ApplyRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApplyRequestController extends Controller
{
    public function getUnreadNotifications($userId)
    {
        $projects = User::find($userId)->projects()->get('id');
        $projectsId = $projects->map(function ($project) {
            return $project->id;
        })->toArray();

        if ($projectsId !== []) {
            $notifications = ApplyRequest::whereIn('project_id', $projectsId)
                                        ->where('is_read', false)
                                        ->get();

            return response()->json(['notifications' => $notifications], Response::HTTP_OK);
        }

        return response(null, Response::HTTP_OK);
    }

    public function getNotifications($userId)
    {
        $projects = User::find($userId)->projects()->get('id');
        $projectsId = $projects->map(function ($project) {
            return $project->id;
        })->toArray();

        $readNotifications = ApplyRequest::whereIn('project_id', $projectsId)
                                        ->where('is_read', true)
                                        ->get();
        $unreadNotifications = ApplyRequest::whereIn('project_id', $projectsId)
                                        ->where('is_read', false)
                                        ->get();

        // 未読・既読通知を取得後、未読を既読にする。
        $unreadNotifications = ApplyRequest::whereIn('project_id', $projectsId)
                                            ->where('is_read', false)
                                            ->update(['is_read' => true]);

        return response()->json([
            'readNotifications' => $readNotifications,
            'unreadNotifications' => $unreadNotifications,
        ], Response::HTTP_OK);
    }

    public function acceptRequest($userId, $projectId)
    {
        ApplyRequest::where([
            ['user_id', $userId],
            ['project_id', $projectId],
        ])->update([
            'is_accepted' => true,
        ]);

        return response(null, Response::HTTP_OK);
    }

    public function denyRequest($userId, $projectId)
    {
        ApplyRequest::where([
            ['user_id', $userId],
            ['project_id', $projectId],
        ])->update([
            'is_accepted' => false,
        ]);

        return response(null, Response::HTTP_OK);
    }
}
