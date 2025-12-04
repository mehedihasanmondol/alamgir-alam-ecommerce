# অ্যাপয়েন্টমেন্ট সিস্টেম ইমপ্লিমেন্টেশন

## ✅ সম্পন্ন (Completed)

### 1. Database & Models
- ✅ `chambers` table migration created
- ✅ `appointments` table migration created
- ✅ `Chamber` model with operating hours logic
- ✅ `Appointment` model with scopes and relationships
- ✅ `AppointmentService` for business logic

### 2. Permissions & Settings
- ✅ 6 appointment permissions added to `RolePermissionSeeder`:
  - `appointments.view`
  - `appointments.confirm`
  - `appointments.cancel`
  - `appointments.complete`
  - `appointments.delete`
  - `chambers.manage`
  
- ✅ 5 appointment settings added to `SiteSettingSeeder`:
  - `appointment_enabled` - Enable/disable system
  - `appointment_heading` - Form heading (Bengali)
  - `appointment_alert_message` - Alert before form
  - `appointment_success_message` - Success message
  - `appointment_default_chamber` - Default chamber ID

---

## 🔄 পরবর্তী ধাপ (Next Steps)

### Step 1: Run Migrations & Seeders
```bash
php artisan migrate
php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SiteSettingSeeder
```

### Step 2: Create Chamber Seeder
Create `database/seeders/ChamberSeeder.php`:
```php
public function run(): void
{
    Chamber::create([
        'name' => 'ঢাকা চেম্বার',
        'slug' => 'dhaka-chamber',
        'address' => 'ঢাকা, বাংলাদেশ',
        'phone' => '01700000000',
        'operating_hours' => [
            'saturday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'sunday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'monday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'tuesday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'wednesday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'thursday' => ['open' => '09:00', 'close' => '17:00', 'is_open' => true],
            'friday' => ['is_open' => false], // শুক্রবার বন্ধ
        ],
        'closed_days' => ['friday'], // শুক্রবার বন্ধ
        'slot_duration' => 30, // 30 মিনিট স্লট
        'is_active' => true,
    ]);
}
```

### Step 3: Livewire Components Needed

#### Frontend: `app/Livewire/Appointment/AppointmentForm.php`
```php
class AppointmentForm extends Component
{
    public $chambers;
    public $chamber_id;
    public $customer_name;
    public $customer_email;
    public $customer_mobile;
    public $customer_address;
    public $appointment_date;
    public $appointment_time;
    public $available_slots = [];
    public $notes;
    public $reason;
    
    public function mount()
    {
        $this->chambers = Chamber::active()->ordered()->get();
        $this->chamber_id = \App\Models\SiteSetting::get('appointment_default_chamber', 1);
    }
    
    public function updated$ChamberId($value)
    {
        $this->available_slots = [];
        $this->appointment_time = null;
    }
    
    public function updatedAppointmentDate($value)
    {
        if ($this->chamber_id && $value) {
            $chamber = Chamber::find($this->chamber_id);
            $service = app(AppointmentService::class);
            $this->available_slots = $service->getAvailableSlots($chamber, $value);
        }
    }
    
    public function submit()
    {
        $this->validate([
            'chamber_id' => 'required|exists:chambers,id',
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'customer_mobile' => 'required|string|max:20',
            'appointment_date' => 'required|date|after:today',
            'appointment_time' => 'required',
        ]);
        
        $service = app(AppointmentService::class);
        $appointment = $service->create($this->all());
        
        session()->flash('success', \App\Models\SiteSetting::get('appointment_success_message'));
        $this->reset();
    }
}
```

#### Admin: `app/Livewire/Admin/AppointmentTable.php`
```php
class AppointmentTable extends Component
{
    public $search = '';
    public $statusFilter = 'all';
    public $chamberFilter = 'all';
    public $dateFilter = 'all';
    public $perPage = 20;
    
    public function confirm($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $service = app(AppointmentService::class);
        $service->confirm($appointment, auth()->id());
        
        session()->flash('success', 'Appointment confirmed successfully');
    }
    
    public function cancel($appointmentId, $reason = null)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $service = app(AppointmentService::class);
        $service->cancel($appointment, auth()->id(), $reason);
        
        session()->flash('success', 'Appointment cancelled');
    }
    
    public function complete($appointmentId)
    {
        $appointment = Appointment::findOrFail($appointmentId);
        $service = app(AppointmentService::class);
        $service->complete($appointment, auth()->id());
        
        session()->flash('success', 'Appointment marked as completed');
    }
    
    public function render()
    {
        $appointments = Appointment::with(['chamber', 'user'])
            ->when($this->search, function($q) {
                $q->where(function($query) {
                    $query->where('customer_name', 'like', '%'.$this->search.'%')
                          ->orWhere('customer_email', 'like', '%'.$this->search.'%')
                          ->orWhere('customer_mobile', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter !== 'all', fn($q) => $q->where('status', $this->statusFilter))
            ->when($this->chamberFilter !== 'all', fn($q) => $q->where('chamber_id', $this->chamberFilter))
            ->orderByRaw("CASE WHEN status = 'pending' THEN 1 WHEN status = 'confirmed' THEN 2 ELSE 3 END")
            ->orderBy('appointment_date')
            ->orderBy('appointment_time')
            ->paginate($this->perPage);
            
        $stats = app(AppointmentService::class)->getStatistics();
        
        return view('livewire.admin.appointment-table', compact('appointments', 'stats'));
    }
}
```

### Step 4: Views

#### Appointment Form View: `resources/views/livewire/appointment/appointment-form.blade.php`
Key features:
- Chamber selector dropdown
- Date picker with disabled Fridays (use Flatpickr or native)
- Time slot selector (shows only available slots)
- Alert message at top
- Success message flash
- Bangla text support

#### Admin Table View: `resources/views/livewire/admin/appointment-table.blade.php`
Key features:
- Stats cards (pending, confirmed, completed, cancelled)
- Search, filter by status/chamber/date
- Action buttons: Confirm, Cancel, Complete
- Pending appointments shown first
- Responsive table

### Step 5: Author Profile Layout Update

Update `resources/views/frontend/blog/author.blade.php`:

```blade
<!-- Before articles section -->
<div class="grid lg:grid-cols-5 gap-8 mb-12">
    <!-- 60% Featured Feedback -->
    <div class="lg:col-span-3">
        <x-feedback.featured-list :author="$author" />
    </div>
    
    <!-- 40% Sticky Appointment Form -->
    <div class="lg:col-span-2">
        <div class="lg:sticky lg:top-24">
            @if(\App\Models\SiteSetting::get('appointment_enabled'))
                @livewire('appointment.appointment-form')
            @else
                <div class="bg-gray-50 p-8 rounded-lg text-center">
                    <p class="text-gray-600">Appointment booking coming soon</p>
                </div>
            @endif
        </div>
    </div>
</div>
```

### Step 6: Routes

#### `routes/web.php`:
```php
// Appointment Routes (Public)
Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
Route::get('/appointments/slots/{chamber}/{date}', [AppointmentController::class, 'getSlots'])->name('appointments.slots');
```

#### `routes/admin.php`:
```php
// Appointment Management
Route::middleware(['auth', 'permission:appointments.view'])->group(function() {
    Route::get('/appointments', [\App\Http\Controllers\Admin\AppointmentController::class, 'index'])
        ->name('admin.appointments.index');
    Route::post('/appointments/{appointment}/confirm', [\App\Http\Controllers\Admin\AppointmentController::class, 'confirm'])
        ->name('admin.appointments.confirm')->middleware('permission:appointments.confirm');
    Route::post('/appointments/{appointment}/cancel', [\App\Http\Controllers\Admin\AppointmentController::class, 'cancel'])
        ->name('admin.appointments.cancel')->middleware('permission:appointments.cancel');
    Route::post('/appointments/{appointment}/complete', [\App\Http\Controllers\Admin\AppointmentController::class, 'complete'])
        ->name('admin.appointments.complete')->middleware('permission:appointments.complete');
        
    // Chamber Management
    Route::resource('chambers', \App\Http\Controllers\Admin\ChamberController::class)
        ->middleware('permission:chambers.manage');
});
```

### Step 7: Admin Menu

Add to `resources/views/layouts/admin.blade.php`:
```blade
@if(auth()->user()->hasPermission('appointments.view'))
<div class="pt-4 pb-2">
    <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Appointments</p>
</div>

<a href="{{ route('admin.appointments.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.appointments.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
    <i class="fas fa-calendar-check w-5 mr-3"></i>
    <span>Appointments</span>
    @php
        $pendingCount = \App\Models\Appointment::pending()->count();
    @endphp
    @if($pendingCount > 0)
        <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
    @endif
</a>

<a href="{{ route('admin.chambers.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.chambers.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
    <i class="fas fa-building w-5 mr-3"></i>
    <span>Chambers</span>
</a>
@endif
```

---

## 📋 Summary of Files Created

1. ✅ `database/migrations/2025_11_26_025300_create_chambers_table.php`
2. ✅ `database/migrations/2025_11_26_025301_create_appointments_table.php`
3. ✅ `app/Models/Chamber.php`
4. ✅ `app/Models/Appointment.php`
5. ✅ `app/Services/AppointmentService.php`
6. ✅ Updated `database/seeders/RolePermissionSeeder.php`
7. ✅ Updated `database/seeders/SiteSettingSeeder.php`

---

## 🎯 Next Actions Required

1. Run migrations: `php artisan migrate`
2. Run seeders: `php artisan db:seed --class=RolePermissionSeeder` and `SiteSettingSeeder`
3. Create ChamberSeeder and add sample chambers
4. Create Livewire components (AppointmentForm, AppointmentTable)
5. Create controllers (AppointmentController, Admin\AppointmentController, Admin\ChamberController)
6. Create views for appointment form and admin table
7. Update author profile with 60/40 sticky layout
8. Add routes
9. Add admin menu items
10. Test complete workflow

The foundation is complete. Ready for frontend/admin implementation!
