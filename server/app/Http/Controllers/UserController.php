<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function linkWithProvider($provider)
    {
        try {
            $user = Auth::user();

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
}
