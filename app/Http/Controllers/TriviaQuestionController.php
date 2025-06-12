<?php

namespace App\Http\Controllers;

use App\Models\TriviaQuestion;
use App\Models\UserScore;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TriviaQuestionController extends Controller
{
    public function create(Request $request)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['agri', 'sangukab'])) {
            return response()->json(['error' => 'Unauthorized. Only agri or sangukab users can create trivia.'], 403);
        }

        $request->validate([
            'category' => 'required|string|in:Reduce,Reuse,Recycle,Gardening',
            'title' => 'required|string|max:255',
            'facts' => 'required|string',
            'question' => 'required|string',
            'correct_answer' => 'required|string',
            'answers' => 'required|array|min:2|max:4',
            'answers.*' => 'required|string',
        ]);

        $question = TriviaQuestion::create([
            'category' => $request->category,
            'title' => $request->title,
            'facts' => $request->facts,
            'question' => $request->question,
            'correct_answer' => $request->correct_answer,
            'answers' => $request->answers,
            'correct_count' => 0,
            'wrong_count' => 0,
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => "Created a Trivia and Question for today",
            'details' => json_encode(['trivia_quiz_id' => $question->id]),
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

        $user = auth()->user();
        $trivia = TriviaQuestion::where('id', $id)
            ->whereDate('created_at', now()->toDateString())
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

        // Update correct_count or wrong_count in trivia_questions
        $trivia->increment($isCorrect ? 'correct_count' : 'wrong_count');

        return response()->json([
            'message' => $isCorrect ? 'Correct answer!' : 'Wrong answer!',
            'correct' => $isCorrect,
            'correct_answer' => $trivia->correct_answer,
        ]);
    }

    public function destroy($id)
    {
        $user = auth()->user();

        if (!$user || !in_array($user->role, ['agri', 'sangukab'])) {
            return response()->json(['error' => 'Unauthorized. Only agri or sangukab users can delete trivia.'], 403);
        }

        $question = TriviaQuestion::findOrFail($id);
        $question->delete();

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => "Deleted a Trivia and Question",
            'details' => json_encode(['trivia_quiz_id' => $question->id]),
        ]);

        return response()->json(['message' => 'Question deleted successfully']);
    }
    
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
