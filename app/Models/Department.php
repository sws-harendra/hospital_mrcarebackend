<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];


     public function hospitals()
    {
        return $this->belongsToMany(Hospital::class, 'hospital_department')
                    ->withPivot('status')
                    ->withTimestamps();
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_department')
                    ->withPivot('hospital_id', 'status')
                    ->withTimestamps();
    }
}
