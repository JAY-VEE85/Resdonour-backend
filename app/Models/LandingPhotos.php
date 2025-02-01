<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPhotos extends Model
{
    use HasFactory;

    protected $fillable = ['images'];

    protected $casts = [
        'images' => 'array', // Ensures JSON is handled properly
    ];
}