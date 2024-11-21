<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPost extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = [ 'user_id' ,'image', 'title', 'content', 'category', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function usersWhoLiked()
    {
        return $this->belongsToMany(User::class, 'likes', 'id', 'user_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'likes', 'id', 'user_id')->withTimestamps();
    }

}

