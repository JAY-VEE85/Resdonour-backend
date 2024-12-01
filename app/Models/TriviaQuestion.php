<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TriviaQuestion extends Model
{
    use HasFactory;

    protected $fillable = ['question', 'correct_answer', 'answers'];

    protected $casts = [
        'answers' => 'array', 
    ];

    public function userScores()
    {
        return $this->hasMany(UserScore::class);
    }
}
