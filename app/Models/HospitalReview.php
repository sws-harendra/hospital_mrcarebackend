<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HospitalReview extends Model
{
     use HasFactory;

    protected $fillable = [
        'hospital_id', 'name', 'email', 'rating', 'comment', 'image'
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
