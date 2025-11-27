<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class HospitalPhoto extends Model
{
     use HasFactory;

    protected $table = 'hospital_photos';

    protected $fillable = [
        'hospital_id',
        'photo_path',
        'caption',
        'sort_order',
        'is_primary',
        'status'
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'status' => 'boolean'
    ];

    public function hospital()
    {
        return $this->belongsTo(Hospital::class);
    }

    /**
     * Set as primary photo - automatically update others
     */
    public function setAsPrimary()
    {
        // Pehle isi hospital ke sabhi photos ko non-primary karein
        $this->hospital->photos()->update(['is_primary' => false]);
        
        // Phir is photo ko primary karein
        $this->update(['is_primary' => true]);
    }

    /**
     * Scope for active photos
     */
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    /**
     * Scope for primary photo
     */
    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    /**
     * Scope ordered by sort order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('created_at');
    }
}
