<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoctorAppointment extends Model
{
    use HasFactory;

    protected $table = 'doctor_appointments';

    protected $fillable = [
        'doctor_id',
        'hospital_id',
        'name',
        'phone_number',
        'email',
        'appointment_date',
        'appointment_time',
        'message',
        'status',
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
    
    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
}
