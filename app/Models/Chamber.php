<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * Chamber Model
 * 
 * Represents doctor chambers/branches for appointment booking
 * 
 * @category Models
 * @package  App\Models
 * @created  2025-11-26
 */
class Chamber extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'address',
        'phone',
        'email',
        'description',
        'operating_hours',
        'closed_days',
        'slot_duration',
        'break_start',
        'break_duration',
        'is_active',
        'display_order',
    ];

    protected $casts = [
        'operating_hours' => 'array',
        'closed_days' => 'array',
        'is_active' => 'boolean',
        'slot_duration' => 'integer',
        'break_start' => 'integer',
        'break_duration' => 'integer',
        'display_order' => 'integer',
    ];

    /**
     * Boot function to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($chamber) {
            if (empty($chamber->slug)) {
                $chamber->slug = Str::slug($chamber->name);
            }
        });
    }

    /**
     * Get appointments for this chamber
     */
    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    /**
     * Get pending appointments
     */
    public function pendingAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->where('status', 'pending');
    }

    /**
     * Get confirmed appointments
     */
    public function confirmedAppointments(): HasMany
    {
        return $this->hasMany(Appointment::class)->where('status', 'confirmed');
    }

    /**
     * Check if chamber is open on a specific day
     */
    public function isOpenOnDay(string $day): bool
    {
        $day = strtolower($day);
        
        if (!$this->operating_hours || !isset($this->operating_hours[$day])) {
            return false;
        }

        return $this->operating_hours[$day]['is_open'] ?? false;
    }

    /**
     * Check if a specific date is closed
     */
    public function isDateClosed(string $date): bool
    {
        if (!$this->closed_days) {
            return false;
        }

        $dayName = strtolower(date('l', strtotime($date)));
        
        // Check if day name is in closed days
        if (in_array($dayName, array_map('strtolower', $this->closed_days))) {
            return true;
        }

        // Check if specific date is in closed days
        if (in_array($date, $this->closed_days)) {
            return true;
        }

        return false;
    }

    /**
     * Get available time slots for a specific date
     */
    public function getAvailableTimeSlots(string $date): array
    {
        if ($this->isDateClosed($date)) {
            return [];
        }

        $dayName = strtolower(date('l', strtotime($date)));
        
        if (!$this->isOpenOnDay($dayName)) {
            return [];
        }

        $hours = $this->operating_hours[$dayName];
        $openTime = strtotime($hours['open']);
        $closeTime = strtotime($hours['close']);
        $slotDuration = $this->slot_duration * 60; // Convert to seconds

        $slots = [];
        $currentTime = $openTime;

        while ($currentTime < $closeTime) {
            // Check if current slot is during break time
            $minutesFromStart = ($currentTime - $openTime) / 60;
            
            if ($this->break_start && $this->break_duration) {
                $breakEnd = $this->break_start + $this->break_duration;
                
                if ($minutesFromStart >= $this->break_start && $minutesFromStart < $breakEnd) {
                    $currentTime += $slotDuration;
                    continue;
                }
            }

            $slots[] = date('H:i', $currentTime);
            $currentTime += $slotDuration;
        }

        return $slots;
    }

    /**
     * Scope: Active chambers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Ordered by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('name');
    }
}
