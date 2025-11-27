<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DoctorReview extends Model
{
    protected $fillable = ['doctor_id', 'name', 'email', 'image', 'rating', 'comment'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
