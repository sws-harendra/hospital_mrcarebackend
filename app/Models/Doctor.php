<?php
// app/Models/Doctor.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    use HasFactory;

    protected $table = 'doctors';

    protected $fillable = [
        'doctor_id',
        'doctor_registration_number',
        'name',
        'email',
        'specialization',
        'qualification',
        'experience',
        'gender',
        'date_of_birth',
        'age',
        'mobile_number',
        'whatsapp_number',
        'services',
        'education',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'bio',
        'website',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'youtube',
        'google_map',
        'latitude',
        'longitude',
        'profile_image',
        'status',
        'is_popular',
        'consultation_fee',
    ];

    protected $casts = [
        'services' => 'array',
        'status' => 'boolean',
        'is_popular' => 'boolean',
        'date_of_birth' => 'date'
    ];

    /**
     * Boot method to generate unique doctor_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($doctor) {
            if (empty($doctor->doctor_id)) {
                $doctor->doctor_id = 'DOC' . date('Ymd') . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Get services as array
     */
    public function getServicesArrayAttribute()
    {
        return $this->services ?: [];
    }


    /**
     * Relationship with business hours
     */
    public function businessHours()
    {
        return $this->hasMany(DoctorBusinessHour::class);
    }

    /**
     * Get business hours for a specific day
     */
    public function getBusinessHoursForDay($day)
    {
        return $this->businessHours()->where('day', $day)->first();
    }

    /**
     * Check if doctor is open now
     */
    public function isOpenNow()
    {
        $currentDay = strtolower(now()->englishDayOfWeek);
        $currentTime = now()->format('H:i:s');

        $businessHour = $this->getBusinessHoursForDay($currentDay);

        if (!$businessHour || $businessHour->is_closed) {
            return false;
        }

        return $currentTime >= $businessHour->open_time && $currentTime <= $businessHour->close_time;
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'doctor_department')
            ->withPivot('hospital_id', 'status')
            ->withTimestamps();
    }

    public function hospitals()
    {
        return $this->belongsToMany(Hospital::class, 'doctor_department')
            ->withPivot('department_id', 'status')
            ->withTimestamps();
    }

    /**
     * All photos for the doctor (ordered: primary first, then sort_order, then newest)
     */
    public function photos()
    {
        return $this->hasMany(DoctorPhoto::class)->orderByDesc('is_primary')->orderBy('sort_order')->orderByDesc('created_at');
    }

    /**
     * Convenient relation for the primary photo
     */
    public function primaryPhoto()
    {
        return $this->hasOne(DoctorPhoto::class)->where('is_primary', true);
    }

    public function reviews()
    {
        return $this->hasMany(DoctorReview::class);
    }



}