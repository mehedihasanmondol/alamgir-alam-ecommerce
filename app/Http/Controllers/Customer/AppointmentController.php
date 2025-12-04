<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    /**
     * Display customer's appointments
     */
    public function index()
    {
        $appointments = Appointment::where('user_id', auth()->id())
            ->with('chamber')
            ->orderBy('appointment_date', 'desc')
            ->orderBy('appointment_time', 'desc')
            ->paginate(15);

        return view('customer.appointments.index', compact('appointments'));
    }

    /**
     * Display appointment details
     */
    public function show(Appointment $appointment)
    {
        // Ensure appointment belongs to customer
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        return view('customer.appointments.show', compact('appointment'));
    }

    /**
     * Cancel an appointment
     */
    public function cancel(Request $request, Appointment $appointment)
    {
        // Ensure appointment belongs to customer
        if ($appointment->user_id !== auth()->id()) {
            abort(403);
        }

        // Check if already cancelled
        if ($appointment->status === 'cancelled') {
            return redirect()->back()->with('error', 'Appointment is already cancelled');
        }

        // Check if appointment is in the past
        if ($appointment->appointment_date < now()->toDateString()) {
            return redirect()->back()->with('error', 'Cannot cancel past appointments');
        }

        $appointment->update([
            'status' => 'cancelled',
            'cancelled_by' => auth()->id(),
            'cancelled_at' => now(),
            'cancellation_reason' => $request->input('reason'),
        ]);

        return redirect()->route('customer.appointments.index')
            ->with('success', 'Appointment cancelled successfully');
    }
}
