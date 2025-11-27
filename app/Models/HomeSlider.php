<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HomeSlider extends Model
{
    protected $table = 'home_slider';

    protected $fillable = [
        'title',
        'subtitle',
        'image',
        'link',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];
}
