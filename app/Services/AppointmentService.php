<?php

namespace App\Services;

use App\Models\Appointment;
use App\Models\User;
use App\Models\Chamber;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Appointment Service
 * 
 * Handles appointment booking logic and auto-customer creation
 * 
 * @category Services
 * @package  App\Services
 * @created  2025-11-26
 */
class AppointmentService
{
    /**
     * Create a new appointment
     * Auto-creates customer if doesn't exist
     */
    public function create(array $data): Appointment
    {
        DB::beginTransaction();
        
        try {
            // Find or create customer
            $user = $this->findOrCreateCustomer([
                'email' => $data['customer_email'],
                'mobile' => $data['customer_mobile'],
                'name' => $data['customer_name'],
                'address' => $data['customer_address'] ?? null,
            ]);

            // Create appointment
            $appointment = Appointment::create([
                'user_id' => $user->id,
                'chamber_id' => $data['chamber_id'],
                'customer_name' => $data['customer_name'],
                'customer_email' => $data['customer_email'],
                'customer_mobile' => $data['customer_mobile'],
                'customer_address' => $data['customer_address'] ?? null,
                'appointment_date' => $data['appointment_date'],
                'appointment_time' => $data['appointment_time'],
                'notes' => $data['notes'] ?? null,
                'reason' => $data['reason'] ?? null,
                'status' => 'pending',
            ]);

            DB::commit();
            
            return $appointment;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Find existing customer or create new one
     */
    protected function findOrCreateCustomer(array $data): User
    {
        // Try to find by email first
        $user = User::where('email', $data['email'])->first();
        
        if ($user) {
            return $user;
        }

        // Try to find by mobile
        $user = User::where('mobile', $data['mobile'])->first();
        
        if ($user) {
            return $user;
        }

        // Create new customer
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mobile' => $data['mobile'],
            'address' => $data['address'],
            'password' => Hash::make(Str::random(16)), // Random password
            'role' => 'customer',
            'is_active' => true,
            'email_verified_at' => now(), // Auto-verify for appointment customers
        ]);

        // Assign customer role
        $customerRole = \App\Modules\User\Models\Role::where('slug', 'customer')->first();
        if ($customerRole) {
            $user->roles()->attach($customerRole->id);
        }

        return $user;
    }

    /**
     * Confirm appointment
     */
    public function confirm(Appointment $appointment, int $adminId): Appointment
    {
        $appointment->update([
            'status' => 'confirmed',
            'confirmed_by' => $adminId,
            'confirmed_at' => now(),
        ]);

        return $appointment->fresh();
    }

    /**
     * Cancel appointment
     */
    public function cancel(Appointment $appointment, int $adminId, ?string $reason = null): Appointment
    {
        $appointment->update([
            'status' => 'cancelled',
            'cancelled_by' => $adminId,
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
        ]);

        return $appointment->fresh();
    }

    /**
     * Mark appointment as completed
     */
    public function complete(Appointment $appointment, int $adminId): Appointment
    {
        $appointment->update([
            'status' => 'completed',
            'completed_by' => $adminId,
            'completed_at' => now(),
        ]);

        return $appointment->fresh();
    }

    /**
     * Check if time slot is available
     */
    public function isTimeSlotAvailable(int $chamberId, string $date, string $time): bool
    {
        return !Appointment::where('chamber_id', $chamberId)
            ->where('appointment_date', $date)
            ->where('appointment_time', $time)
            ->whereIn('status', ['pending', 'confirmed'])
            ->exists();
    }

    /**
     * Get available slots for a chamber on a specific date
     */
    public function getAvailableSlots(Chamber $chamber, string $date): array
    {
        $allSlots = $chamber->getAvailableTimeSlots($date);
        
        // Get booked slots
        $bookedSlots = Appointment::where('chamber_id', $chamber->id)
            ->where('appointment_date', $date)
            ->whereIn('status', ['pending', 'confirmed'])
            ->pluck('appointment_time')
            ->map(fn($time) => date('H:i', strtotime($time)))
            ->toArray();

        // Filter out booked slots
        return array_values(array_diff($allSlots, $bookedSlots));
    }

    /**
     * Get appointments statistics
     */
    public function getStatistics(): array
    {
        return [
            'total' => Appointment::count(),
            'pending' => Appointment::pending()->count(),
            'confirmed' => Appointment::confirmed()->count(),
            'completed' => Appointment::completed()->count(),
            'cancelled' => Appointment::cancelled()->count(),
            'today' => Appointment::today()->count(),
            'upcoming' => Appointment::upcoming()->count(),
        ];
    }
}
