<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangayPost extends Model
{
    use HasFactory;

    protected $table = 'barangay_posts';

    protected $fillable = [
        'user_id',
        'caption',
        'images',
    ];

    protected $casts = [
        'images' => 'array',
    ];
}
