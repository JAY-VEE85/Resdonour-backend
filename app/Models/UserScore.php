<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScore extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'question_id', 'answer', 'is_correct', 'score'];

    public function triviaQuestion()
    {
        return $this->belongsTo(TriviaQuestion::class, 'question_id'); 
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

