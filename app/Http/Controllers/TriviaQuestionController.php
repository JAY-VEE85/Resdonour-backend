<?php

namespace App\Http\Controllers;

use App\Models\TriviaQuestion;
use App\Models\UserScore;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TriviaQuestionController extends Controller
{
    public function create(Request $request)
    {
        // Validate input
        $request->validate([
            'category' => 'required|string|in:Reduce,Reuse,Recycle,Gardening',
            'title' => 'required|string|max:255',
            'facts' => 'required|string',
            'question' => 'required|string',
            'correct_answer' => 'required|string',
            'answers' => 'required|array|min:2|max:4', // Ensure at least 2 and max 4 choices
            'answers.*' => 'required|string',
        ]);

        // Create new trivia question
        $question = TriviaQuestion::create([
            'category' => $request->category,
            'title' => $request->title,
            'facts' => $request->facts,
            'question' => $request->question,
            'correct_answer' => $request->correct_answer,
            'answers' => $request->answers, // Store answers as JSON
            'correct_count' => 0, // Initialize count
            'wrong_count' => 0,   // Initialize count
        ]);

        return response()->json($question, 201);
    }

    public function getTriviaToday()
    {
        $todayTrivia = TriviaQuestion::whereDate('created_at', now()->toDateString())->first();

        if (!$todayTrivia) {
            return response()->json(['message' => 'No trivia created today'], 404);
        }

        return response()->json($todayTrivia);
    }

    public function index()
    {
        $questions = TriviaQuestion::all();
        return response()->json($questions);
    }

    public function getById($id)
    {
        $question = TriviaQuestion::findOrFail($id);

        $scoreStats = DB::table('user_scores')
            ->selectRaw('
                SUM(CASE WHEN is_correct = 1 THEN 1 ELSE 0 END) as correct_answers,
                SUM(CASE WHEN is_correct = 0 THEN 1 ELSE 0 END) as wrong_answers
            ')
            ->where('trivia_question_id', $id)
            ->first();

        return response()->json([
            'question' => $question,
            'correct_answers' => $scoreStats->correct_answers ?? 0,
            'wrong_answers' => $scoreStats->wrong_answers ?? 0,
        ]);
    }

    public function update(Request $request, $id)
    {
        $question = TriviaQuestion::findOrFail($id);

        $request->validate([
            'category' => 'required|string|in:Reduce,Reuse,Recycle,Gardening',
            'title' => 'required|string|max:255',
            'facts' => 'required|string',
            'question' => 'required|string',
            'correct_answer' => 'required|string',
            'answers' => 'array',
            'answers.*' => 'string',
        ]);

        $question->update([
            'category' => $request->category,
            'title' => $request->title,
            'facts' => $request->facts,
            'question' => $request->question,
            'correct_answer' => $request->correct_answer,
            'answers' => $request->answers,
        ]);

        return response()->json($question);
    }

    public function submitAnswer(Request $request, $id)
    {
        $request->validate([
            'answer' => 'required|string',
        ]);

        $user = auth()->user(); // Get authenticated user
        $trivia = TriviaQuestion::where('id', $id)
            ->whereDate('created_at', now()->toDateString()) // ✅ Ensure it's today's trivia
            ->first();

        if (!$trivia) {
            return response()->json(['message' => 'Trivia not found or not available today.'], 404);
        }

        // Check if the user has already answered
        $existingAnswer = UserScore::where('user_id', $user->id)
            ->where('trivia_question_id', $trivia->id)
            ->first();

        if ($existingAnswer) {
            return response()->json([
                'message' => 'You have already answered this trivia.',
                'correct' => $existingAnswer->is_correct
            ], 409);
        }

        // Determine if the answer is correct
        $isCorrect = $request->answer === $trivia->correct_answer;

        // Store the user's answer
        UserScore::create([
            'user_id' => $user->id,
            'trivia_question_id' => $trivia->id,
            'user_answer' => $request->answer,
            'is_correct' => $isCorrect,
        ]);

        // ✅ Update correct_count or wrong_count in trivia_questions
        $trivia->increment($isCorrect ? 'correct_count' : 'wrong_count');

        return response()->json([
            'message' => $isCorrect ? 'Correct answer!' : 'Wrong answer!',
            'correct' => $isCorrect,
            'correct_answer' => $trivia->correct_answer, // ✅ Include correct answer in response
        ]);
    }

    public function destroy($id)
    {
        $question = TriviaQuestion::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Question deleted successfully']);
    }
    
    // user
    public function getUserTriviaStats($userId)
    {
        $totalAnswered = UserScore::totalAnswered($userId);
        $totalCorrect = UserScore::totalCorrect($userId);
        $totalWrong = UserScore::totalWrong($userId);

        return response()->json([
            'user_id' => $userId,
            'total_answered' => $totalAnswered,
            'total_correct' => $totalCorrect,
            'total_wrong' => $totalWrong,
        ]);
    }
}
