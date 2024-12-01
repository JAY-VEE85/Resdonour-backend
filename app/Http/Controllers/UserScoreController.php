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
}

