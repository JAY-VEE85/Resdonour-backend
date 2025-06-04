<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserPostsController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\TriviaQuestionController;
use App\Http\Controllers\UserScoreController;
use App\Http\Controllers\BarangayPostsController;
use App\Http\Controllers\VisitController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
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
Route::post('/verify-email-with-code', [VerifyEmailController::class, 'verifyWithCode']);
Route::post('/email/resend', [VerifyEmailController::class, 'resendVerificationCode']);

// user login/register
Route::post('/register', [AuthController::class, 'register']);
Route::get('/users', [AuthController::class, 'index']);
Route::post('/login', [AuthController::class, 'login']);
Route::delete('/cancel-registration', [AuthController::class, 'cancelRegistration']);
Route::match(['post', 'delete'],'/cancel-due-refresh', [AuthController::class, 'cancelDueRefresh']);

// forgot password
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/verify-token', [AuthController::class, 'verifyToken']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// user logout
Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

// user account update
Route::middleware('auth:sanctum')->get('/getUser', [UserController::class, 'getUser']);
Route::middleware('auth:sanctum')->put('/update', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->post('/verify-current-password', [UserController::class, 'verifyCurrentPassword']);
Route::middleware('auth:sanctum')->post('/change-password', [UserController::class, 'changePassword']);

// posts route for user side
Route::middleware('auth:sanctum')->post('/post', [UserPostsController::class, 'createPost']); 
Route::middleware('auth:sanctum')->get('/post/{id}', [UserPostsController::class, 'getPost']);
Route::middleware('auth:sanctum')->get('/getUserPosts', [UserPostsController::class, 'getUserPosts']); // user posts
Route::middleware('auth:sanctum')->post('/updatepost/{id}', [UserPostsController::class, 'updatePost']);
Route::middleware('auth:sanctum')->delete('/userdeletepost/{id}', [UserPostsController::class, 'deletePost']);
Route::middleware('auth:sanctum')->get('/all-posts', [UserPostsController::class, 'getAllPosts']); // all users posts
Route::middleware('auth:sanctum')->get('/liked-posts', [UserPostsController::class, 'getLikedPosts']); // user liked posts
Route::middleware('auth:sanctum')->post('/posts/{postId}/report', [UserPostsController::class, 'reportPost']); // reporting user posts //

// posts route for admin side
Route::middleware('auth:sanctum')->get('/allPost', [AdminController::class, 'allPost']);
Route::middleware('auth:sanctum')->get('/userpost/{id}', [AdminController::class, 'getPost']);
Route::middleware('auth:sanctum')->put('/removepost/{id}', [AdminController::class, 'softDeletePost']);
Route::middleware('auth:sanctum')->get('/allReportedPosts', [AdminController::class, 'allReportedPosts']);
// mag add view removed post then pwede nila idiretso delete don

// for landing page photos
Route::middleware('auth:sanctum')->post('/addphotos', [AdminController::class, 'addphotos']);
Route::get('/showlatestphoto', [AdminController::class, 'showlatestphoto']); // without auth kasi nasa landing page
Route::middleware('auth:sanctum')->get('/showallphotos', [AdminController::class, 'showallphotos']);
Route::middleware('auth:sanctum')->post('/editLatestPhoto', [AdminController::class, 'editLatestPhoto']);
Route::middleware('auth:sanctum')->delete('/deletephoto/{id}', [AdminController::class, 'deletephoto']);
Route::middleware('auth:sanctum')->post('/deleteAllPhotos', [AdminController::class, 'deleteAllPhotos']);

// for landing page visit
Route::get('/totalvisits', [VisitController::class, 'getTotalVisits']);
Route::post('/landingpagevisit', [VisitController::class, 'addLandingPageVisit']);


// for admin dashboard
Route::middleware('auth:sanctum')->get('/dashboardStatistics', [AdminController::class, 'dashboardStatistics']);
Route::middleware('auth:sanctum')->get('/most-active-users', [ReportController::class, 'getMostActiveUsers']);
Route::middleware('auth:sanctum')->get('/most-liked-posts', [ReportController::class, 'getMostLikedPosts']);
Route::middleware('auth:sanctum')->get('/tableCategories', [ReportController::class, 'tableCategories']);
Route::middleware('auth:sanctum')->get('/chartMaterials', [ReportController::class, 'chartMaterials']);

// likedpost of each user
Route::middleware('auth:sanctum')->post('/posts/{id}/toggle-like', [UserPostsController::class, 'toggleLike']);
Route::middleware('auth:sanctum')->get('/posts/{id}/total-likes', [UserPostsController::class, 'getTotalLikes']);

// report generation
// Route::middleware('auth:sanctum')->get('/getReport', [ReportController::class, 'getReport']);

// for report tab
Route::middleware('auth:sanctum')->get('/materialsCount', [ReportController::class, 'getMaterialsCount']);
Route::middleware('auth:sanctum')->get('/mostCategories', [ReportController::class, 'mostCategories']);
Route::middleware('auth:sanctum')->get('/topUsers', [ReportController::class, 'getUserStats']);
Route::middleware('auth:sanctum')->get('/topLiked', [ReportController::class, 'get20MostLikedPosts']);

// trivia 
Route::middleware('auth:sanctum')->prefix('trivia')->group(function () {
    Route::get('getquestions', [TriviaQuestionController::class, 'index']);

    // para kay admin
    Route::post('questions', [TriviaQuestionController::class, 'create']);
    Route::get('triviaByID/{id}', [TriviaQuestionController::class, 'getById']);
    Route::put('questions/{id}', [TriviaQuestionController::class, 'update']);
    Route::delete('question/{id}', [TriviaQuestionController::class, 'destroy']); 

    // for user to answer trivia for today
    Route::get('triviatoday', [TriviaQuestionController::class, 'getTriviaToday']);
    Route::post('questions/{id}/answer', [TriviaQuestionController::class, 'submitAnswer']);

    // user score routes
    Route::get('user/score/{id}', [UserScoreController::class, 'getScores']);
    Route::get('user/score', [UserScoreController::class, 'getUserScores']);
    Route::get('alluser/correctscores', [UserScoreController::class, 'getAllCorrectScores']);
});

// badge for users
// Route::middleware('auth:sanctum')->get('/user/{id}/award-badge', [AdminController::class, 'awardBadge']);
// Route::middleware('auth:sanctum')->post('/user/{id}/remove-badge', [AdminController::class, 'removeBadge']);

// for announcement users view
Route::middleware('auth:sanctum')->get('/user/announcements', [UserPostsController::class, 'getAnnouncements']);

// for announcement admin
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/announcements', [AnnouncementController::class, 'store']);
    Route::get('/getannouncements', [AnnouncementController::class, 'index']);
    Route::get('/announcements/{id}', [AnnouncementController::class, 'show']);
    Route::put('/announcements/{id}', [AnnouncementController::class, 'update']);
    Route::delete('/announcements/{id}', [AnnouncementController::class, 'destroy']);
});

// Barangay Posts (CRUD operations)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/barangay-posts', [BarangayPostsController::class, 'createPost']); // Create a post
    Route::get('/barangay-posts', [BarangayPostsController::class, 'getAllPosts']); // Get all posts
    Route::get('/barangay-posts/{id}', [BarangayPostsController::class, 'getPost']); // Get a specific post
    Route::put('/barangay-posts/{id}', [BarangayPostsController::class, 'updatePost']); // Update a post
    Route::delete('/barangay-posts/{id}', [BarangayPostsController::class, 'deletePost']); // Delete a post
});

// users tab
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