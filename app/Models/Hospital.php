<?php
// app/Models/Hospital.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    protected $table = 'hospitals';

    protected $fillable = [
        'hospital_id',
        'name',
        'email',
        'phone_number',
        'whatsapp_number',
        'emergency_number',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'website',
        'description',
        'logo',
        'latitude',
        'longitude',
        'status',
        'is_popular',
        'is_featured',
        'is_verified',
        'facebook',
        'twitter',
        'linkedin',
        'instagram',
        'youtube',
        'google_map',
        'hospital_registration_number',
        'license_number',
        'established_date',
        'number_of_beds',
        'number_of_doctors',
        'number_of_nurses',
        'number_of_departments',
        'hospital_type',
        'ownership_type',
        'accreditations'
    ];

    protected $casts = [
        'status' => 'boolean',
        'is_popular' => 'boolean',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
        'established_date' => 'date'
    ];

    /**
     * Boot method to generate unique hospital_id
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hospital) {
            if (empty($hospital->hospital_id)) {
                $hospital->hospital_id = 'HOSP' . date('Ymd') . str_pad(static::count() + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    /**
     * Relationship with business hours
     */
    public function businessHours()
    {
        return $this->hasMany(HospitalBusinessHour::class);
    }

    /**
     * Get business hours for a specific day
     */
    public function getBusinessHoursForDay($day)
    {
        return $this->businessHours()->where('day', $day)->first();
    }

    /**
     * Check if hospital is open now
     */
    public function isOpenNow()
    {
        $currentDay = strtolower(now()->englishDayOfWeek);
        $currentTime = now()->format('H:i:s');

        $businessHour = $this->getBusinessHoursForDay($currentDay);

        if (!$businessHour) {
            return false;
        }

        // Agar closed hai toh false return karein
        if ($businessHour->is_closed) {
            return false;
        }

        // Agar 24/7 emergency hai toh always open
        if ($businessHour->is_emergency_24_7) {
            return true;
        }

        // Time check karein
        if ($businessHour->open_time && $businessHour->close_time) {
            return $currentTime >= $businessHour->open_time->format('H:i:s') &&
                $currentTime <= $businessHour->close_time->format('H:i:s');
        }

        return false;
    }

    /**
     * Check if emergency services are available 24/7
     */
    public function isEmergency24_7()
    {
        return $this->businessHours()->where('is_emergency_24_7', true)->exists();
    }

    /**
     * Get today's business hours
     */
    public function getTodayHours()
    {
        $currentDay = strtolower(now()->englishDayOfWeek);
        return $this->getBusinessHoursForDay($currentDay);
    }


    /**
     * Relationship with photos
     */
    public function photos()
    {
        return $this->hasMany(HospitalPhoto::class);
    }

    /**
     * Get primary photo
     */
    public function primaryPhoto()
    {
        return $this->hasOne(HospitalPhoto::class)->where('is_primary', true);
    }

    /**
     * Get active photos
     */
    public function activePhotos()
    {
        return $this->photos()->active()->ordered();
    }

    /**
     * Get photos count
     */
    public function getPhotosCountAttribute()
    {
        return $this->photos()->count();
    }

    /**
     * Get primary photo URL
     */
    public function getPrimaryPhotoUrlAttribute()
    {
        $primaryPhoto = $this->primaryPhoto;
        if ($primaryPhoto) {
            return asset($primaryPhoto->photo_path);
        }

        // Agar primary photo nahi hai toh koi active photo lein
        $firstPhoto = $this->activePhotos()->first();
        return $firstPhoto ? asset($firstPhoto->photo_path) : null;
    }

    /**
     * Check if hospital has photos
     */
    public function hasPhotos()
    {
        return $this->photos()->exists();
    }

    public function departments()
    {
        return $this->belongsToMany(Department::class, 'hospital_department')
            ->withPivot('status')
            ->withTimestamps();
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_department')
            ->withPivot('department_id', 'status')
            ->withTimestamps();
    }

    public function reviews()
    {
        // Assuming HospitalReview model exists and uses 'hospital_id' as the foreign key
        return $this->hasMany(HospitalReview::class, 'hospital_id', 'id');
    }
}