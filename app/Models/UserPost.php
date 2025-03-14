<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class UserPost extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'id';
    protected $fillable = [
        'user_id',
        'image',
        'title',
        'content',
        'category',
        'status',
        'remarks',
        'report_count',
        'report_remarks',
        'badge',
        'remarks',
        'total_likes',
        'created_at'
    ];

    protected $dates = ['deleted_at'];

    protected static function boot()
    {
        parent::boot();

        // auto delete in 7 days
        static::retrieved(function () {
            UserPost::whereNotNull('deleted_at')
                ->where('deleted_at', '<=', Carbon::now()->subDays(7))
                ->forceDelete();
        });
    }

    public static function get20MostLikedPosts()
    {
        $previousLikes = null;
        $topRankCount = 0;
        $lastAssignedRank = 1;

        return self::with('user')
            ->where('total_likes', '>', 0)
            ->orderByDesc('total_likes')
            ->take(20)
            ->get()
            ->map(function ($post, $index) use (&$previousLikes, &$topRankCount, &$lastAssignedRank) {

                $badge = self::getPostBadge($post->total_likes, $index, $previousLikes, $topRankCount, $lastAssignedRank);
                $previousLikes = $post->total_likes;

                return [
                    'author_name' => $post->user->fname . ' ' . $post->user->lname,
                    'post_title'  => $post->title,
                    'total_likes' => $post->total_likes,
                    'badge'       => $badge,
                ];
            });
    }

    private static function getPostBadge($likes, $index, $previousLikes, &$topRankCount, &$lastAssignedRank)
    {
        if ($likes < 20 && $topRankCount < 5) {
            if ($likes !== $previousLikes) {
                $lastAssignedRank = $topRankCount + 1;
            }

            $topRankCount++;
            return "Top " . $lastAssignedRank . " Most Liked Post";
        }

        // Assign badges for specific like thresholds
        if ($likes >= 50) {
            return "Gold Post";
        } elseif ($likes >= 30) {
            return "Silver Post";
        } elseif ($likes >= 20) {
            return "Bronze Post";
        }

        return null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
        return $this->belongsTo(User::class, 'user_id');
    }

    public function likedByUsers()
    {
        return $this->belongsToMany(User::class, 'post_likes', 'post_id', 'user_id')->withTimestamps();
    }

    public function likes()
    {
        return $this->hasMany(PostLike::class, 'post_id');
    }
}

