@extends('layouts.customer')

@section('title', 'Appointment Details')

@section('content')
<div class="bg-white rounded-lg shadow-sm">
    <!-- Header -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Appointment Details</h1>
                <p class="mt-1 text-sm text-gray-600">View your appointment information</p>
            </div>
            <a href="{{ route('customer.appointments.index') }}" 
               class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Back to List
            </a>
        </div>
    </div>

    <!-- Appointment Details -->
    <div class="p-6">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Chamber Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Chamber Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Chamber Name:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->chamber->name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Address:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->chamber->address }}</p>
                    </div>
                    @if($appointment->chamber->phone)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Phone:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->chamber->phone }}</p>
                    </div>
                    @endif
                    @if($appointment->chamber->email)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Email:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->chamber->email }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Appointment Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Appointment Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Date:</span>
                        <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('l, F d, Y') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Time:</span>
                        <p class="mt-1 text-gray-900">{{ \Carbon\Carbon::parse($appointment->appointment_time)->format('h:i A') }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Status:</span>
                        <div class="mt-1">
                            @php
                                $statusColors = [
                                    'pending' => 'bg-yellow-100 text-yellow-800',
                                    'confirmed' => 'bg-green-100 text-green-800',
                                    'cancelled' => 'bg-red-100 text-red-800',
                                    'completed' => 'bg-blue-100 text-blue-800',
                                ];
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ $statusColors[$appointment->status] ?? 'bg-gray-100 text-gray-800' }}">
                                {{ ucfirst($appointment->status) }}
                            </span>
                        </div>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Booking ID:</span>
                        <p class="mt-1 text-gray-900 font-mono">#{{ str_pad($appointment->id, 6, '0', STR_PAD_LEFT) }}</p>
                    </div>
                </div>
            </div>

            <!-- Patient Information -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Patient Information</h2>
                <div class="space-y-3">
                    <div>
                        <span class="text-sm font-medium text-gray-500">Name:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->customer_name }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Email:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->customer_email }}</p>
                    </div>
                    <div>
                        <span class="text-sm font-medium text-gray-500">Mobile:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->customer_mobile }}</p>
                    </div>
                    @if($appointment->customer_address)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Address:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->customer_address }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Additional Details -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Additional Details</h2>
                <div class="space-y-3">
                    @if($appointment->reason)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Reason for Visit:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->reason }}</p>
                    </div>
                    @endif
                    
                    @if($appointment->notes)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Notes:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->notes }}</p>
                    </div>
                    @endif

                    @if($appointment->status === 'cancelled' && $appointment->cancellation_reason)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Cancellation Reason:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->cancellation_reason }}</p>
                    </div>
                    @endif

                    @if($appointment->admin_notes)
                    <div>
                        <span class="text-sm font-medium text-gray-500">Admin Notes:</span>
                        <p class="mt-1 text-gray-900">{{ $appointment->admin_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        @if($appointment->status !== 'cancelled' && $appointment->status !== 'completed' && $appointment->appointment_date >= now()->toDateString())
        <div class="mt-6 pt-6 border-t border-gray-200">
            <div class="flex justify-end">
                <button onclick="cancelAppointment()" 
                        class="inline-flex items-center px-4 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Cancel Appointment
                </button>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Cancel Confirmation Modal (Product-style with backdrop blur) -->
<div id="cancelModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="fixed inset-0 transition-opacity" 
             style="background-color: rgba(0, 0, 0, 0.4); backdrop-filter: blur(4px); -webkit-backdrop-filter: blur(4px);"
             onclick="closeCancelModal()"></div>
        
        <div class="relative rounded-lg shadow-xl max-w-md w-full p-6 border border-gray-200"
             style="background-color: rgba(255, 255, 255, 0.95); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px);">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full mb-4">
                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 text-center mb-2">Cancel Appointment</h3>
            <p class="text-sm text-gray-500 text-center mb-6">
                Are you sure you want to cancel this appointment? This action cannot be undone.
            </p>
            
            <form action="{{ route('customer.appointments.cancel', $appointment) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason for cancellation (optional)</label>
                    <textarea name="reason" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>
                
                <div class="flex gap-3">
                    <button type="button" onclick="closeCancelModal()" 
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Close
                    </button>
                    <button type="submit" 
                            class="flex-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        Confirm Cancellation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function cancelAppointment() {
    document.getElementById('cancelModal').classList.remove('hidden');
}

function closeCancelModal() {
    document.getElementById('cancelModal').classList.add('hidden');
}
</script>
@endpush
@endsection
