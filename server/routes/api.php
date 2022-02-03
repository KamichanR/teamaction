<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('auth/login', [LoginController::class, 'login'])
    ->middleware('guest');

Route::post('auth/logout', [LogoutController::class, 'logout'])
    ->middleware('auth');

Route::get('auth/{provider}', [UserController::class, 'linkWithProvider'])
    ->middleware('auth');

// ユーザー情報編集に必要なデータを取得
Route::get('data/user/edit', [UserController::class, 'getEditData']);

// ユーザー情報を取得（個別ページ）
Route::get('data/user/{userId}', [UserController::class, 'getUserData'])
    ->where('userid', '[0-9]+');

// ユーザー情報の更新
Route::patch('update/user', [UserController::class, 'updateUser']);

// プロジェクト作成に必要な情報を取得
Route::get('data/project', [ProjectController::class, 'getCreateData']);

// プロジェクトの作成
Route::post('create/project', [ProjectController::class, 'createProject']);

// プロジェクト情報を取得
Route::get('data/project/{projectId}', [ProjectController::class, 'readProject'])
    ->where('userid', '[0-9]+');

// プロジェクト情報の更新
Route::patch('update/project', [ProjectController::class, 'updateProject']);

// プロジェクトの削除
Route::delete('delete/project/{projectId}', [ProjectController::class, 'deleteProject'])
    ->where('projectId', '[0-9]+');

// ユーザー情報を取得（ユーザー一覧ページ）
Route::get('data/users/{group}', [UserController::class, 'getUsersData'])
    ->where('group', '[0-9]+');

// プロジェクト情報を取得（プロジェクト一覧ページ）
Route::get('data/projects/{group}', [ProjectController::class, 'getProjectsData'])
    ->where('group', '[0-9]+');

// フォロー情報の更新
Route::post('update/follow/{userId}', [UserController::class, 'updateFollow'])
    ->where('userId', '[0-9]+');

// お気に入り情報の更新
Route::post('update/favorite/{projectId}', [ProjectController::class, 'updateFavorite'])
    ->where('projectId', '[0-9]+');
