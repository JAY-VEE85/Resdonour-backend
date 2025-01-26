<?php

namespace App\Http\Controllers;

use App\Models\UserScore;
use App\Models\user;
use App\Models\TriviaQuestion;
use Illuminate\Http\Request;

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

    public function getAllUsersScores()
    {
        $usersScores = UserScore::with('user')
            ->selectRaw('user_id, SUM(score) as total_score')
            ->groupBy('user_id')
            ->get();

        $result = $usersScores->map(function ($userScore) {
            $user = $userScore->user;
            $fullName = $user ? "{$user->fname} {$user->lname}" : 'Unknown';

            return [
                'user_name' => $fullName,
                'user_id' => $userScore->user_id,
                'total_score' => $userScore->total_score,
            ];
        });

        return response()->json([
            'message' => 'All user scores retrieved successfully',
            'users' => $result,
        ]);
    }


    

}

