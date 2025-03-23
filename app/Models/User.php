<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

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
        return $this->hasMany(UserPost::class, 'user_id');
    }

    // for liked posts
    public function likedPosts()
    {
        return $this->belongsToMany(UserPost::class, 'post_likes', 'user_id', 'post_id')->withTimestamps();
    }

    // for age and badges
    protected $appends = ['age', 'badge'];

    // for age
    public function getAgeAttribute()
    {
        return Carbon::parse($this->birthdate)->age;
    }

    // for badge
    public function getBadgeAttribute()
    {
        $postCount = $this->posts()->count();

        if ($postCount >= 10) {
            $topUsers = Cache::remember('top_users', 60, function () {
                return User::withCount('posts')
                    ->orderByDesc('posts_count')
                    ->limit(5)
                    ->pluck('id')
                    ->toArray();
            });

            // Check if the user is in the top 5
            $rank = array_search($this->id, $topUsers);
            return $rank !== false ? "Top " . ($rank + 1) : "Contributor";
        } elseif ($postCount >= 6) {
            return "Contributor";
        } else {
            return "Newbie";
        }
    }

    // top users
    public static function getUsersWithPostAndLikeStats()
    {
        return self::whereNotIn('role', ['admin', 'agri'])
            ->withCount('posts')
            ->withSum('posts', 'total_likes')
            ->get()
            ->map(function ($user) {
                return [
                    'full_name'   => "{$user->fname} {$user->lname}",
                    'total_posts' => $user->posts_count,
                    'total_likes' => $user->posts_sum_total_likes ?? 0,
                    'badge'       => $user->badge
                ];
            });
    }

    // Function to assign badge ranking
    private static function assignBadge($totalPosts, $totalLikes)
    {
        if ($totalPosts >= 50 || $totalLikes >= 100) {
            return ['badge' => 'Top 1', 'priority' => 1];
        } elseif ($totalPosts >= 40 || $totalLikes >= 80) {
            return ['badge' => 'Top 2', 'priority' => 2];
        } elseif ($totalPosts >= 30 || $totalLikes >= 60) {
            return ['badge' => 'Top 3', 'priority' => 3];
        } elseif ($totalPosts >= 20 || $totalLikes >= 40) {
            return ['badge' => 'Top 4', 'priority' => 4];
        } elseif ($totalPosts >= 10 || $totalLikes >= 20) {
            return ['badge' => 'Top 5', 'priority' => 5];
        } elseif ($totalPosts >= 5 || $totalLikes >= 10) {
            return ['badge' => 'Contributor', 'priority' => 6];
        } else {
            return ['badge' => 'Newbie', 'priority' => 7];
        }
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
