<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
    {
        $user = User::firstOrNew([
            'uid' => $request->uid,
        ]);

        if (!$user->exists) $user = $this->createUser($user, $request);

        Auth::login($user);

        return response(null, Response::HTTP_OK);
    }

    public function createUser(User $user, LoginRequest $request)
    {
        $user->username = $request->username ?? 'TeamActionUser'.mt_rand(0, 9999999999);
        $user->save();

        return User::find($user->id);
    }
}
