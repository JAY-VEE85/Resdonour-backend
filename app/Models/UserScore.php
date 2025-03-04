<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserScore extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'trivia_question_id', 'user_answer', 'is_correct'];

    // Relationship with User
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relationship with TriviaQuestion
    public function triviaQuestion() {
        return $this->belongsTo(TriviaQuestion::class);
    }

    public function scopeTotalAnswered($query, $userId) {
        return $query->where('user_id', $userId)->count();
    }
    
    public function scopeTotalCorrect($query, $userId) {
        return $query->where('user_id', $userId)->where('is_correct', true)->count();
    }
    
    public function scopeTotalWrong($query, $userId) {
        return $query->where('user_id', $userId)->where('is_correct', false)->count();
    }
}

