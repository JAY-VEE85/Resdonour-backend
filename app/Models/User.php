<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'fname',
        'lname',
        'email',
        'phone_number',
        'city',
        'barangay',
        'street',
        'birthdate',
        'password',
        'badge',
        'role',
        'verification_code'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // User.php
    public function posts()
    {
        return $this->hasMany(UserPost::class);
    }

    // public function likedPosts()
    // {
    //     return $this->belongsToMany(UserPost::class, 'likes', 'user_id', 'id')->withTimestamps();
    // }

    public function likedPosts()
    {
        return $this->belongsToMany(UserPost::class, 'likes', 'user_id', 'post_id')->withTimestamps();
    }

    public function awardBadge($badge)
    {
        $currentBadges = json_decode($this->badges, true) ?? [];
        if (!in_array($badge, $currentBadges)) {
            $currentBadges[] = $badge;
            $this->badges = json_encode($currentBadges);
            $this->save();
        }
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmail);
    }
}
