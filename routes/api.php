<?php

use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPostsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\TriviaQuestionController;
use App\Http\Controllers\UserScoreController;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Controllers\VerifyEmailController;
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

// email verification
Route::get('/email/verify', function (Request $request) {
    return response()->json(['message' => 'Please verify your email']);
})->name('verification.notice');

// Route::get('/verify-email/{id}/{hash}', [VerifyEmailController::class, 'verify'])
// ->middleware(['signed'])
// ->name('verification.verify');

Route::post('/verify-email-with-code', [VerifyEmailController::class, 'verifyWithCode']);

// send new email verification code
Route::post('/email/resend', [VerifyEmailController::class, 'resendVerificationCode']);

// User login/Register
Route::post('/register', [AuthController::class, 'register']);
Route::get('/users', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);
Route::delete('/cancel-registration', [AuthController::class, 'cancelRegistration']);
Route::match(['post', 'delete'],'/cancel-due-refresh', [AuthController::class, 'cancelDueRefresh']);

// forgot password na wala pa sa front
Route::post('user/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('user/reset-password', [AuthController::class, 'resetPassword']);

// user logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// user account update
Route::middleware('auth:sanctum')->get('/getUser', [UserController::class, 'getUser']);
Route::middleware('auth:sanctum')->put('/update', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->post('/verify-current-password', [UserController::class, 'verifyCurrentPassword']);
Route::middleware('auth:sanctum')->post('/change-password', [UserController::class, 'changePassword']);

// user post
Route::middleware('auth:sanctum')->post('/post', [UserPostsController::class, 'posts']); 
Route::middleware('auth:sanctum')->get('/post/{id}', [UserPostsController::class, 'getPost']);
Route::middleware('auth:sanctum')->get('/getUserPosts', [UserPostsController::class, 'getUserPosts']);
Route::middleware('auth:sanctum')->post('/updatepost/{id}', [UserPostsController::class, 'updatePost']);
Route::middleware('auth:sanctum')->delete('/userdeletepost/{id}', [UserPostsController::class, 'deletePost']);

// all post for user
Route::middleware('auth:sanctum')->get('/posts', [UserPostsController::class, 'getAllPosts']);

// all post for admin
Route::middleware('auth:sanctum')->get('/allPost', [AdminController::class, 'allPost']);
Route::middleware('auth:sanctum')->get('/userpost/{id}', [AdminController::class, 'getPost']);
Route::middleware('auth:sanctum')->delete('/deletepost/{id}', [AdminController::class, 'deletePost']);

// for landing page photos
Route::middleware('auth:sanctum')->post('/addphotos', [AdminController::class, 'addphotos']);
Route::get('/showphotos', [AdminController::class, 'showphotos']); // without auth kasi nasa landing page
Route::middleware('auth:sanctum')->get('/showallphotos', [AdminController::class, 'showallphotos']);
Route::middleware('auth:sanctum')->delete('/deletephoto/{id}', [AdminController::class, 'deletephoto']);
Route::middleware('auth:sanctum')->post('/deleteAllPhotos', [AdminController::class, 'deleteAllPhotos']);

// approval para sa admins(agri and admin)
Route::middleware('auth:sanctum')->patch('/post/{id}/approve', [AdminController::class, 'approvePost']);
Route::middleware('auth:sanctum')->patch('/post/{id}/decline', [AdminController::class, 'declinePost']);

// for pending, approved and declined post
Route::middleware('auth:sanctum')->get('/totalusers', [AdminController::class, 'totalUsers']);
Route::middleware('auth:sanctum')->get('/totalPosts', [AdminController::class, 'totalPosts']);
Route::middleware('auth:sanctum')->get('/totalPendings', [AdminController::class, 'totalPendings']);
Route::middleware('auth:sanctum')->get('/totalApproved', [AdminController::class, 'totalApproved']);
Route::middleware('auth:sanctum')->get('/totalDeclined', [AdminController::class, 'totalDeclined']);

// likedpost of each user
Route::middleware('auth:sanctum')->post('/post/{id}/like', [UserPostsController::class, 'toggleLikePost']);
Route::middleware('auth:sanctum')->get('/user/liked-posts', [UserPostsController::class, 'getLikedPosts']);
Route::middleware('auth:sanctum')->get('/posts/{id}/total-likes', [UserPostsController::class, 'getTotalLikesForPosts']);

// report generation
Route::middleware('auth:sanctum')->get('/getReport', [ReportController::class, 'getReport']);
Route::middleware('auth:sanctum')->get('/userTotalPost', [ReportController::class, 'totalPost']);  // for pie
Route::middleware('auth:sanctum')->get('/userTotalPosts', [ReportController::class, 'totalPosts']); // for table

Route::middleware('auth:sanctum')->get('/likechart', [ReportController::class, 'totalLike']); // for pie
Route::middleware('auth:sanctum')->get('/liketable', [ReportController::class, 'topliked']); // for pie

Route::middleware('auth:sanctum')->get('/oldestpending', [ReportController::class, 'oldestPending']);

// trivia 
Route::middleware('auth:sanctum')->prefix('trivia')->group(function () {
    Route::post('questions', [TriviaQuestionController::class, 'create']);  // admin to
    Route::get('getquestions', [TriviaQuestionController::class, 'index']);   // For users to view all questions

    // para kay admin
    Route::put('questions/{id}', [TriviaQuestionController::class, 'update']);
    Route::delete('question/{id}', [TriviaQuestionController::class, 'destroy']); 

    // para sa sagot ni user
    Route::post('questions/{question_id}/answer', [UserScoreController::class, 'store']);
    Route::get('user/score/{id}', [UserScoreController::class, 'getScores']);
    Route::get('alluser/scores', [UserScoreController::class, 'getAllUsersScores']);
    Route::get('user/score', [UserScoreController::class, 'getUserScores']);

});

// badge for users
Route::middleware('auth:sanctum')->get('/user/{id}/award-badge', [AdminController::class, 'awardBadge']);
Route::middleware('auth:sanctum')->post('/user/{id}/remove-badge', [AdminController::class, 'removeBadge']);

// for announcement users
Route::middleware('auth:sanctum')->get('/user/announcements', [UserPostsController::class, 'getAnnouncements']);

// for announcement admin
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::get('/getannouncements', [AnnouncementController::class, 'index']);
    Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->get('/users', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->delete('/delete/user', [UserController::class, 'destroy']);


// backend done, modif nalang if need hehe

// test if API is working

Route::get('/test', function () {
    return response()->json([
        'success' => true,
        'message' => 'API is working correctly!'
    ]);
});