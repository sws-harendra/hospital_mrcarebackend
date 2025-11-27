<?php
// app/Models/HospitalBusinessHour.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HospitalBusinessHour extends Model
{
    use HasFactory;

    protected $table = 'hospital_business_hours';

    protected $fillable = [
        'hospital_id',
        'day',
        'open_time',
        'close_time',
        'is_closed',
        'is_emergency_24_7'
    ];

    protected $casts = [
        'is_closed' => 'boolean',
        'is_emergency_24_7' => 'boolean',
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i'
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}