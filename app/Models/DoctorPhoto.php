<?php


namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DoctorPhoto extends Model
{
    protected $fillable = [
        'doctor_id',
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

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function scopeActive(Builder $query)
    {
        return $query->where('status', true);
    }

    // simple helper to set this photo as primary
    public function setAsPrimary()
    {
        static::where('doctor_id', $this->doctor_id)->update(['is_primary' => false]);
        $this->update(['is_primary' => true]);
    }
}