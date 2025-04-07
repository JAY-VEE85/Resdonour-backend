<?php

namespace App\Http\Controllers;

use App\Models\UserScore;
use App\Models\user;
use App\Models\TriviaQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserScoreController extends Controller
{
    public function store(Request $request, $question_id)
    {
        $user = auth()->user();

        $existingAnswer = UserScore::where('user_id', $user->id)
            ->where('question_id', $question_id)
            ->first();

        if ($existingAnswer) {
            return response()->json([
                'message' => 'You have already answered this question.',
            ], 403); 
        }

        $question = TriviaQuestion::findOrFail($question_id);
        $is_correct = $request->answer === $question->correct_answer;
        $score = $is_correct ? 1 : 0;

        $userScore = UserScore::create([
            'user_id' => $user->id,
            'question_id' => $question->id,
            'answer' => $request->answer,
            'is_correct' => $is_correct,
            'score' => $score,
        ]);

        return response()->json(['message' => 'Answer recorded', 'score' => $score]);
    }

    public function getScores(Request $request, $id)
    {
        // Find the user by the passed id
        $user = User::find($id);

        // Check if the user exists
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Construct the user's name
        $userName = trim(($user->fname ?? '') . ' ' . ($user->lname ?? ''));

        // Fetch scores for the specific user
        $scores = UserScore::with('triviaQuestion')  // Ensure 'triviaQuestion' is loaded
            ->where('user_id', $user->id)             // Only get scores for this user
            ->get();

        // If no scores exist for the user, return a message
        if ($scores->isEmpty()) {
            return response()->json(['message' => 'No scores found for this user'], 404);
        }

        // Calculate total score
        $totalScore = $scores->sum('score');

        // Group scores by trivia question ID
        $triviaScores = $scores->groupBy(function ($score) {
            return $score->triviaQuestion->id ?? 'unknown';  // Group by trivia question ID
        })->map(function ($group) {
            return [
                'total_score' => $group->sum('score'),
                'correct_answers' => $group->where('is_correct', true)->count(),
                'questions' => $group->map(function ($item) {
                    return [
                        'question_id' => $item->triviaQuestion->id,  // Correct trivia question ID
                        'question_text' => $item->triviaQuestion->question ?? 'No question available',  // Question text
                        'answer' => $item->answer,
                        'is_correct' => $item->is_correct,
                        'correct_answer' => $item->triviaQuestion->correct_answer,  // Correct answer
                    ];
                })
            ];
        });

        // Return the data
        return response()->json([
            'message' => 'Scores retrieved successfully',
            'user_name' => $userName,
            'total_score' => $totalScore,
            'trivia_scores' => $triviaScores
        ]);
    }

    public function getUserScores()
    {
        $user = auth()->user();
        $scores = UserScore::where('user_id', $user->id)->get();

        return response()->json($scores);
    }

    // all users score for envi-admin
    public function getAllCorrectScores()
    {
        $scores = DB::table('user_scores')
            ->join('users', 'user_scores.user_id', '=', 'users.id')
            ->select(
                'user_scores.user_id',
                DB::raw("CONCAT(users.fname, ' ', users.lname) AS full_name"),
                DB::raw('SUM(CASE WHEN user_scores.is_correct = true THEN 1 ELSE 0 END) as correct_answers'),
                DB::raw('COUNT(user_scores.id) as total_answers'),
                'users.phone_number',
                'users.email'
            )
            ->groupBy('user_scores.user_id', 'users.fname', 'users.lname', 'users.phone_number', 'users.email')
            ->get()
            ->map(function ($user) {
                $user->wrong_answers = $user->total_answers - $user->correct_answers;
                return $user;
            });

        return response()->json([
            'message' => 'User scores retrieved successfully',
            'users' => $scores,
        ]);
    }
}

