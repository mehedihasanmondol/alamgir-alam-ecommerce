<?php

namespace App\Livewire\Admin;

use App\Models\Appointment;
use App\Models\Chamber;
use App\Services\AppointmentService;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Admin Appointment Table Livewire Component
 * 
 * Admin panel for managing appointments with filtering,
 * search, and status updates
 * 
 * @category Livewire
 * @package  Admin
 * @created  2025-11-26
 */
class AppointmentTable extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $chamberFilter = 'all';
    public $dateFilter = 'all'; // all, today, upcoming, past
    public $perPage = 20;
    
    // For modals
    public $selectedAppointment = null;
    public $cancellationReason = '';
    public $adminNotes = '';
    public $showCancelModal = false;
    public $showNotesModal = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'chamberFilter' => ['except' => 'all'],
        'dateFilter' => ['except' => 'all'],
    ];

    /**
     * Reset pagination when filters change
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingChamberFilter()
    {
        $this->resetPage();
    }

    public function updatingDateFilter()
    {
        $this->resetPage();
    }

    /**
     * Confirm appointment
     */
    public function confirm($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        
        if (!auth()->user()->hasPermission('appointments.confirm')) {
            session()->flash('error', 'You do not have permission to confirm appointments');
            return;
        }

        $service = app(AppointmentService::class);
        $service->confirm($appointment, auth()->id());
        
        session()->flash('success', 'Appointment confirmed successfully');
    }

    /**
     * Open cancel modal
     */
    public function openCancelModal($appointmentId)
    {
        $this->selectedAppointment = $appointmentId;
        $this->cancellationReason = '';
        $this->showCancelModal = true;
    }

    /**
     * Cancel appointment with reason
     */
    public function cancelAppointment()
    {
        if (!$this->selectedAppointment) {
            return;
        }

        if (!auth()->user()->hasPermission('appointments.cancel')) {
            session()->flash('error', 'You do not have permission to cancel appointments');
            $this->showCancelModal = false;
            return;
        }

        $appointment = Appointment::findOrFail($this->selectedAppointment);
        $service = app(AppointmentService::class);
        $service->cancel($appointment, auth()->id(), $this->cancellationReason);
        
        session()->flash('success', 'Appointment cancelled successfully');
        $this->showCancelModal = false;
        $this->selectedAppointment = null;
        $this->cancellationReason = '';
    }

    /**
     * Complete appointment
     */
    public function complete($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        
        if (!auth()->user()->hasPermission('appointments.complete')) {
            session()->flash('error', 'You do not have permission to complete appointments');
            return;
        }

        $service = app(AppointmentService::class);
        $service->complete($appointment, auth()->id());
        
        session()->flash('success', 'Appointment marked as completed');
    }

    /**
     * Delete appointment
     */
    public function delete($appointmentId)
    {
        if (!auth()->user()->hasPermission('appointments.delete')) {
            session()->flash('error', 'You do not have permission to delete appointments');
            return;
        }

        $appointment = Appointment::findOrFail($appointmentId);
        $appointment->delete();
        
        session()->flash('success', 'Appointment deleted successfully');
    }

    /**
     * Open admin notes modal
     */
    public function openNotesModal($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $this->selectedAppointment = $appointmentId;
        $this->adminNotes = $appointment->admin_notes ?? '';
        $this->showNotesModal = true;
    }

    /**
     * Save admin notes
     */
    public function saveNotes()
    {
        if (!$this->selectedAppointment) {
            return;
        }

        $appointment = Appointment::findOrFail($this->selectedAppointment);
        $appointment->update(['admin_notes' => $this->adminNotes]);
        
        session()->flash('success', 'Notes saved successfully');
        $this->showNotesModal = false;
        $this->selectedAppointment = null;
        $this->adminNotes = '';
    }

    /**
     * Get filtered appointments
     */
    public function getAppointmentsProperty()
    {
        return Appointment::with(['chamber', 'user', 'confirmedBy', 'cancelledBy', 'completedBy'])
            ->when($this->search, function($query) {
                $query->where(function($q) {
                    $q->where('customer_name', 'like', '%'.$this->search.'%')
                      ->orWhere('customer_email', 'like', '%'.$this->search.'%')
                      ->orWhere('customer_mobile', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', function($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->chamberFilter !== 'all', function($query) {
                $query->where('chamber_id', $this->chamberFilter);
            })
            ->when($this->dateFilter === 'today', function($query) {
                $query->whereDate('appointment_date', today());
            })
            ->when($this->dateFilter === 'upcoming', function($query) {
                $query->where('appointment_date', '>=', today())
                      ->whereIn('status', ['pending', 'confirmed']);
            })
            ->when($this->dateFilter === 'past', function($query) {
                $query->where('appointment_date', '<', today());
            })
            ->orderByRaw("CASE 
                WHEN status = 'pending' THEN 1 
                WHEN status = 'confirmed' THEN 2 
                WHEN status = 'completed' THEN 3
                ELSE 4 
            END")
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate($this->perPage);
    }

    /**
     * Get statistics
     */
    public function getStatsProperty()
    {
        return app(AppointmentService::class)->getStatistics();
    }

    /**
     * Get chambers for filter
     */
    public function getChambersProperty()
    {
        return Chamber::active()->ordered()->get();
    }

    public function render()
    {
        return view('livewire.admin.appointment-table', [
            'appointments' => $this->appointments,
            'stats' => $this->stats,
            'chambers' => $this->chambers,
        ]);
    }
}
