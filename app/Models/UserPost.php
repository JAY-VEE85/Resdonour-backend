<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPost extends Model
{
    use HasFactory;

    protected $fillable = [ 'user_id' ,'image', 'title', 'content', 'category'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

