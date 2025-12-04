<?php

namespace App\Livewire\Appointment;

use App\Models\Chamber;
use App\Models\Appointment;
use App\Services\AppointmentService;
use Livewire\Component;

/**
 * Appointment Form Livewire Component
 * 
 * Frontend appointment booking form with chamber selection,
 * date/time pickers, and auto-customer creation
 * 
 * @category Livewire
 * @package  Appointment
 * @created  2025-11-26
 */
class AppointmentForm extends Component
{
    public $chambers;
    public $chamber_id;
    public $customer_name = '';
    public $customer_email = '';
    public $customer_mobile = '';
    public $customer_address = '';
    public $appointment_date;
    public $appointment_time;
    public $available_slots = [];
    public $notes = '';
    public $reason = '';
    public $formExpanded = false; // Track if form is expanded
    
    // Settings
    public $heading;
    public $alertMessage;
    public $successMessage;

    protected $listeners = ['dateSelected' => 'handleDateSelected'];

    public function mount()
    {
        // Load active chambers
        $this->chambers = Chamber::active()->ordered()->get();
        
        // Set default chamber
        $defaultChamberId = \App\Models\SiteSetting::get('appointment_default_chamber');
        if ($defaultChamberId && $this->chambers->contains('id', $defaultChamberId)) {
            $this->chamber_id = $defaultChamberId;
        } elseif ($this->chambers->isNotEmpty()) {
            $this->chamber_id = $this->chambers->first()->id;
        }
        
        // Load settings
        $this->heading = \App\Models\SiteSetting::get('appointment_heading', 'অ্যাপয়েন্টমেন্ট বুক করুন');
        $this->alertMessage = \App\Models\SiteSetting::get('appointment_alert_message');
        $this->successMessage = \App\Models\SiteSetting::get('appointment_success_message');
        
        // Prefill if user is logged in
        if (auth()->check()) {
            $user = auth()->user();
            $this->customer_name = $user->name;
            $this->customer_email = $user->email;
            $this->customer_mobile = $user->mobile ?? '';
            $this->customer_address = $user->address ?? '';
        }
    }

    /**
     * When chamber changes, reset date and time
     */
    public function updatedChamberId($value)
    {
        $this->appointment_date = null;
        $this->appointment_time = null;
        $this->available_slots = [];
    }

    /**
     * When date changes, load available time slots and expand form
     */
    public function updatedAppointmentDate($value)
    {
        if ($this->chamber_id && $value) {
            $this->loadAvailableSlots();
            $this->formExpanded = true; // Expand form once date is selected
        }
    }

    /**
     * Load available time slots for selected chamber and date
     */
    public function loadAvailableSlots()
    {
        if (!$this->chamber_id || !$this->appointment_date) {
            $this->available_slots = [];
            return;
        }

        $chamber = Chamber::find($this->chamber_id);
        
        if (!$chamber) {
            $this->available_slots = [];
            return;
        }

        $service = app(AppointmentService::class);
        $this->available_slots = $service->getAvailableSlots($chamber, $this->appointment_date);
        
        // Reset selected time if it's not in available slots
        if ($this->appointment_time && !in_array($this->appointment_time, $this->available_slots)) {
            $this->appointment_time = null;
        }
    }

    /**
     * Get disabled dates for calendar
     */
    public function getDisabledDatesProperty()
    {
        if (!$this->chamber_id) {
            return [];
        }

        $chamber = Chamber::find($this->chamber_id);
        
        if (!$chamber || !$chamber->closed_days) {
            return [];
        }

        return $chamber->closed_days;
    }

    /**
     * Validation rules
     */
    protected function rules()
    {
        return [
            'chamber_id' => 'required|exists:chambers,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_mobile' => 'required|string|max:20',
            'customer_address' => 'nullable|string|max:500',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
            'reason' => 'nullable|string|max:500',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    /**
     * Validation messages in Bangla
     */
    protected function messages()
    {
        return [
            'chamber_id.required' => 'চেম্বার নির্বাচন করুন',
            'customer_name.required' => 'নাম লিখুন',
            'customer_email.email' => 'সঠিক ইমেইল লিখুন',
            'customer_mobile.required' => 'মোবাইল নম্বর লিখুন',
            'appointment_date.required' => 'তারিখ নির্বাচন করুন',
            'appointment_date.after' => 'ভবিষ্যতের তারিখ নির্বাচন করুন',
            'appointment_time.required' => 'সময় নির্বাচন করুন',
        ];
    }

    /**
     * Close/collapse the appointment form
     */
    public function closeForm()
    {
        $this->formExpanded = false;
        $this->dispatch('scrollToForm'); // Dispatch event to scroll to form
    }

    /**
     * Submit appointment form
     */
    public function submit()
    {
        $this->validate();

        try {
            $service = app(AppointmentService::class);
            
            $appointment = $service->create([
                'chamber_id' => $this->chamber_id,
                'customer_name' => $this->customer_name,
                'customer_email' => $this->customer_email,
                'customer_mobile' => $this->customer_mobile,
                'customer_address' => $this->customer_address,
                'appointment_date' => $this->appointment_date,
                'appointment_time' => $this->appointment_time,
                'reason' => $this->reason,
                'notes' => $this->notes,
            ]);

            // Show success message
            session()->flash('appointment_success', $this->successMessage);
            
            // Reset form
            $this->reset(['appointment_date', 'appointment_time', 'reason', 'notes', 'available_slots']);
            
            // If user is not logged in, reset customer info too
            if (!auth()->check()) {
                $this->reset(['customer_name', 'customer_email', 'customer_mobile', 'customer_address']);
            }
            
            // Dispatch success event
            $this->dispatch('appointment-created', ['appointment' => $appointment->id]);
            
        } catch (\Exception $e) {
            session()->flash('appointment_error', 'একটি ত্রুটি হয়েছে। আবার চেষ্টা করুন।');
            \Log::error('Appointment creation failed: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.appointment.appointment-form');
    }
}
