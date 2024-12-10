<?php

namespace App\Http\Controllers;

use App\Models\UserScore;
use App\Models\TriviaQuestion;
use Illuminate\Http\Request;

class UserScoreController extends Controller
{
    public function store(Request $request, $question_id)
    {
        $user = auth()->user();

        // Check if the user already has an answer for this question
        $existingAnswer = UserScore::where('user_id', $user->id)
            ->where('question_id', $question_id)
            ->first();

        if ($existingAnswer) {
            return response()->json([
                'message' => 'You have already answered this question.',
            ], 403); // HTTP 403 Forbidden
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

    public function getScores(Request $request)
    {
        $user = auth()->user();
        
        if (!$user) {
            return response()->json(['message' => 'User not authenticated'], 401);
        }
    
        $scores = UserScore::where('user_id', $user->id)->get();
    
        $totalScore = $scores->sum('score');
    
        return response()->json([
            'message' => 'Scores retrieved successfully',
            'user_name' => $user->name,  
            'total_score' => $totalScore,
            'scores' => $scores
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

