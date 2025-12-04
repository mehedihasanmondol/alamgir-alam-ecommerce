<div id="appointment-form-container" class="bg-white rounded-lg shadow-lg p-6">
    <!-- Heading -->
    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ $heading }}</h2>

    <!-- Alert Message -->
    @if($alertMessage)
    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
        <div class="flex">
            <div class="flex-shrink-0">
                <i class="fas fa-exclamation-triangle text-yellow-400"></i>
            </div>
            <div class="ml-3">
                <p class="text-sm text-yellow-700">{{ $alertMessage }}</p>
            </div>
        </div>
    </div>
    @endif

    

    <!-- Appointment Form -->
    <form wire:submit.prevent="submit" class="space-y-4">
        <!-- Chamber Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                চেম্বার নির্বাচন করুন <span class="text-red-500">*</span>
            </label>
            <select wire:model.live="chamber_id" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">চেম্বার নির্বাচন করুন</option>
                @foreach($chambers as $chamber)
                    <option value="{{ $chamber->id }}">{{ $chamber->name }}</option>
                @endforeach
            </select>
            @error('chamber_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        @if($chamber_id)
        <!-- Date Selection -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                তারিখ নির্বাচন করুন <span class="text-red-500">*</span>
            </label>
            <input type="date" 
                   wire:model.live="appointment_date"
                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('appointment_date') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
            <p class="text-xs text-gray-500 mt-1">* শুক্রবার বন্ধ থাকে</p>
        </div>
        @endif

        <!-- Time Selection -->
        @if(count($available_slots) > 0)
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                সময় নির্বাচন করুন <span class="text-red-500">*</span>
            </label>
            <select wire:model="appointment_time" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <option value="">সময় নির্বাচন করুন</option>
                @foreach($available_slots as $slot)
                    <option value="{{ $slot }}">{{ date('h:i A', strtotime($slot)) }}</option>
                @endforeach
            </select>
            @error('appointment_time') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        @elseif($appointment_date && $chamber_id)
        <div class="bg-gray-50 border border-gray-200 rounded-lg p-4">
            <p class="text-sm text-gray-600">এই তারিখে কোন সময় উপলব্ধ নেই। অন্য তারিখ নির্বাচন করুন।</p>
        </div>
        @endif

        @if($formExpanded)
        <!-- Customer Name -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                আপনার নাম <span class="text-red-500">*</span>
            </label>
            <input type="text" 
                   wire:model="customer_name"
                   placeholder="আপনার পূর্ণ নাম লিখুন"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('customer_name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Customer Email -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                ইমেইল <span class="text-gray-400 text-xs">(ঐচ্ছিক)</span>
            </label>
            <input type="email" 
                   wire:model="customer_email"
                   placeholder="your@email.com"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('customer_email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Customer Mobile -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                মোবাইল নম্বর <span class="text-red-500">*</span>
            </label>
            <input type="tel" 
                   wire:model="customer_mobile"
                   placeholder="01XXXXXXXXX"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('customer_mobile') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Customer Address -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                ঠিকানা
            </label>
            <textarea wire:model="customer_address"
                      rows="2"
                      placeholder="আপনার ঠিকানা লিখুন"
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('customer_address') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Reason -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                দেখানোর কারণ
            </label>
            <input type="text" 
                   wire:model="reason"
                   placeholder="সংক্ষেপে লিখুন"
                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('reason') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <!-- Notes -->
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
                অতিরিক্ত তথ্য
            </label>
            <textarea wire:model="notes"
                      rows="3"
                      placeholder="যদি কিছু বলার থাকে..."
                      class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
            @error('notes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>
        <!-- Success Message -->
        @if (session()->has('appointment_success'))
            <div class="bg-green-50 border-l-4 border-green-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-check-circle text-green-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-green-700">{{ session('appointment_success') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if (session()->has('appointment_error'))
            <div class="bg-red-50 border-l-4 border-red-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700">{{ session('appointment_error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="pt-4 flex gap-3">
            <button type="button" 
                    wire:click="closeForm"
                    class="px-4 py-3 border border-gray-300 text-gray-700 font-medium rounded-lg hover:bg-gray-50 transition duration-200">
                <i class="fas fa-times mr-2"></i>
                বন্ধ করুন
            </button>
            <button type="submit" 
                    class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-6 rounded-lg transition duration-200">
                <i class="fas fa-calendar-check mr-2"></i>
                অ্যাপয়েন্টমেন্ট বুক করুন
            </button>
        </div>
        @endif
    </form>
</div>

<script>
// Listen for scroll event from Livewire
document.addEventListener('livewire:initialized', () => {
    Livewire.on('scrollToForm', () => {
        const formContainer = document.getElementById('appointment-form-container');
        if (formContainer) {
            formContainer.scrollIntoView({ 
                behavior: 'smooth', 
                block: 'start' 
            });
        }
    });
});
</script>
