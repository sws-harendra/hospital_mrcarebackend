<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HospitalAppointment extends Model
{
    use HasFactory;

    // Table name fix
    protected $table = 'hospital_appointments';

    protected $fillable = [
        'hospital_id',
        'department_id', // Added department_id
        'name',
        'phone_number',
        'email',
        'appointment_date',
        'appointment_time',
        'message',
        'status',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        // Note: appointment_time casting is complex for H:i format; remove cast if not needed.
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }
    
    public function department()
    {
        return $this->belongsTo(Department::class);
    }
}
