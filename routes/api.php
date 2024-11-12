<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPostsController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResource('/posts', App\Http\Controllers\Api\PostController::class);

// User login/Register
Route::post('/register', [AuthController::class, 'register']);
Route::get('/users', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);

// user logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// user account update
Route::middleware('auth:sanctum')->post('/update', [UserController::class, 'update']);

// user post
Route::middleware('auth:sanctum')->post('/post', [UserPostsController::class, 'posts']);
Route::middleware('auth:sanctum')->get('/post/{id}', [UserPostsController::class, 'getPost']);
Route::middleware('auth:sanctum')->put('/updatepost/{id}', [UserPostsController::class, 'updatePost']);
Route::middleware('auth:sanctum')->delete('/deletepost/{id}', [UserPostsController::class, 'deletePost']);

// all post
Route::middleware('auth:sanctum')->get('/posts', [UserPostsController::class, 'getAllPosts']);

// approval para sa admins(agri and admin)
Route::middleware('auth:sanctum')->patch('/post/{id}/approve', [AdminController::class, 'approvePost']);
Route::middleware('auth:sanctum')->patch('/post/{id}/decline', [AdminController::class, 'declinePost']);

// for pending, approved and declined post
Route::middleware('auth:sanctum')->get('/totalusers', [AdminController::class, 'totalUsers']);
Route::middleware('auth:sanctum')->get('/totalPosts', [AdminController::class, 'totalPosts']);
Route::middleware('auth:sanctum')->get('/totalPendings', [AdminController::class, 'totalPendings']);
Route::middleware('auth:sanctum')->get('/totalApproved', [AdminController::class, 'totalApproved']);
Route::middleware('auth:sanctum')->get('/totalDeclined', [AdminController::class, 'totalDeclined']);

// backend done, modif nalang if need hehe

