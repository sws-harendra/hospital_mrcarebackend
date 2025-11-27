<?php
// app/Models/DoctorBusinessHour.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DoctorBusinessHour extends Model
{
    use HasFactory;

    protected $table = 'doctor_business_hours';

    protected $fillable = [
        'doctor_id',
        'day',
        'open_time',
        'close_time',
        'is_closed'
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}