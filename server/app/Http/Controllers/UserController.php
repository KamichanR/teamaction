<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function linkWithProvider($provider)
    {
        $id = Auth::id();
        $user = User::find($id);

        $user->linked_with_twitter = true;
        $user->save();

        // switch ($provider) {
        //     case 'twwiter':
        //         $user->update(['linked_with_twitter' => true]);
        //         break;
        //     case 'google':
        //         $user->update(['linked_with_google' => true]);
        //         break;
        //     default:
        //         return response(null, Response::HTTP_INTERNAL_SERVER_ERROR);
        // }

        return response(null, Response::HTTP_OK);
    }
}
