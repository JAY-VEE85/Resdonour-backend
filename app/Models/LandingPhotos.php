<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPhotos extends Model
{
    use HasFactory;

    protected $fillable = [
        'image1', 'content1',
        'image2', 'content2',
        'image3', 'content3',
        'image4', 'content4',
        'image5', 'content5',
    ];
}