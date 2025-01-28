<?php

namespace App\Http\Controllers;

use App\Models\TriviaQuestion;
use Illuminate\Http\Request;

class TriviaQuestionController extends Controller
{
    public function create(Request $request)
    {
        $request->validate([
            'question' => 'required|string',
            'correct_answer' => 'required|string',
            'answers' => 'required|array',
            'answers.*' => 'string',
        ]);

        $question = TriviaQuestion::create([
            'question' => $request->question,
            'correct_answer' => $request->correct_answer,
            'answers' => $request->answers,
        ]);

        return response()->json($question, 201);
    }

    public function index()
    {
        $questions = TriviaQuestion::all();
        return response()->json($questions);
    }

    public function update(Request $request, $id)
    {
        $question = TriviaQuestion::findOrFail($id);

        $request->validate([
            'question' => 'required|string',
            'correct_answer' => 'required|string',
            'answers' => 'array',
            'answers.*' => 'string',
        ]);

        $question->update([
            'question' => $request->question,
            'correct_answer' => $request->correct_answer,
            'answers' => $request->answers,
        ]);

        return response()->json($question);
    }
    
    public function destroy($id)
    {
        $question = TriviaQuestion::findOrFail($id);
        $question->delete();

        return response()->json(['message' => 'Question deleted successfully']);
    }
}
