# Feedback Management System Documentation

**Created:** 2025-11-25  
**Module:** Feedback System  
**Priority:** High

---

## Overview
Complete feedback management system similar to product reviews, allowing customers to submit feedback with images, ratings, and contact information. Includes auto-user registration, admin approval workflow, and featured feedback display.

---

## Database Schema

### Table: `feedback`
```sql
- id (bigint, primary key)
- user_id (foreign key to users, nullable)
- customer_name (string, required)
- customer_email (string, required, indexed)
- customer_mobile (string, required, indexed)
- customer_address (text, nullable)
- rating (integer, 1-5)
- title (string, nullable)
- feedback (text, required)
- images (json, nullable)
- status (enum: pending, approved, rejected, default: pending)
- is_featured (boolean, default: false)
- helpful_count (integer, default: 0)
- not_helpful_count (integer, default: 0)
- approved_at (timestamp, nullable)
- approved_by (foreign key to users, nullable)
- deleted_at (timestamp, soft delete)
- created_at, updated_at (timestamps)
```

**Indexes:**
- `status, approved_at` (composite for filtering)
- `customer_email` (for user lookup)
- `customer_mobile` (for user lookup)

---

## Features

### 1. Frontend Features
- **Feedback Submission Form**
  - Name, Email, Mobile, Address fields
  - 5-star rating system
  - Optional title
  - Feedback textarea
  - Multiple image upload (webp compression)
  - Auto-user registration/login
  - Real-time validation

- **Feedback Display (Author Profile)**
  - 60/40 layout split
  - 60%: Featured/selected feedback
  - 40%: "Appointment Coming Soon" message
  - View More button → Full feedback page

- **Dedicated Feedback Page**
  - All approved feedback
  - Infinite scroll (load 10 at a time)
  - Filter by rating
  - Sort options (recent, helpful, rating)
  - Image lightbox
  - Helpful/Not Helpful voting

### 2. Admin Panel Features
- **Feedback Management Table**
  - List all feedback (pending, approved, rejected)
  - Bulk actions (approve, reject, delete)
  - Filter by status, rating
  - Search by customer name/email
  - Quick approve/reject buttons
  - View customer details
  - Toggle featured status

- **Feedback Details View**
  - Full feedback content
  - Customer information
  - Image gallery
  - Approval controls
  - Admin notes (optional)

### 3. Auto-User Registration Logic
```php
// On feedback submission:
1. Check if email OR mobile exists in users table
2. If exists → Link feedback to existing user
3. If not exists → Create new user:
   - name = customer_name
   - email = customer_email
   - phone = customer_mobile
   - address = customer_address
   - role = 'customer'
   - Auto-login after creation
4. Set feedback status = 'pending'
5. Send confirmation email (optional)
```

### 4. Image Upload System
- **Compression:** All images converted to webp
- **Sizes:** 
  - Original (max 1920px width)
  - Thumbnail (300x300)
  - Medium (800x800)
- **Storage:** `storage/app/public/feedback/{feedback_id}/`
- **Max files:** 5 images per feedback
- **Max size:** 5MB per image

---

## File Structure

### Models
```
app/Models/
└── Feedback.php
```

### Livewire Components
```
app/Livewire/
├── Feedback/
│   ├── FeedbackForm.php (Frontend submission)
│   └── FeedbackList.php (Frontend display with pagination)
└── Admin/
    └── FeedbackTable.php (Admin management)
```

### Controllers
```
app/Http/Controllers/
├── FeedbackController.php (Frontend)
└── Admin/
    └── FeedbackController.php (Admin panel)
```

### Services
```
app/Services/
├── FeedbackService.php (Business logic)
└── ImageCompressionService.php (webp conversion - existing)
```

### Views
```
resources/views/
├── frontend/
│   └── feedback/
│       ├── index.blade.php (All feedback page)
│       └── partials/
│           ├── featured-section.blade.php (60/40 layout)
│           └── feedback-card.blade.php
├── livewire/
│   ├── feedback/
│   │   ├── feedback-form.blade.php
│   │   └── feedback-list.blade.php
│   └── admin/
│       └── feedback-table.blade.php
└── admin/
    └── feedback/
        ├── index.blade.php (Management table)
        └── show.blade.php (Details view)
```

---

## Routes

### Frontend Routes
```php
Route::get('/feedback', [FeedbackController::class, 'index'])->name('feedback.index');
Route::post('/feedback', [FeedbackController::class, 'store'])->name('feedback.store');
Route::post('/feedback/{feedback}/helpful', [FeedbackController::class, 'helpful'])->name('feedback.helpful');
```

### Admin Routes
```php
Route::middleware(['permission:feedback.view'])->group(function () {
    Route::get('/admin/feedback', [Admin\FeedbackController::class, 'index'])->name('admin.feedback.index');
    Route::get('/admin/feedback/{feedback}', [Admin\FeedbackController::class, 'show'])->name('admin.feedback.show');
    Route::post('/admin/feedback/{feedback}/approve', [Admin\FeedbackController::class, 'approve'])->name('admin.feedback.approve');
    Route::post('/admin/feedback/{feedback}/reject', [Admin\FeedbackController::class, 'reject'])->name('admin.feedback.reject');
    Route::post('/admin/feedback/{feedback}/feature', [Admin\FeedbackController::class, 'toggleFeature'])->name('admin.feedback.feature');
    Route::delete('/admin/feedback/{feedback}', [Admin\FeedbackController::class, 'destroy'])->name('admin.feedback.destroy');
});
```

---

## Permissions

Add to `RolePermissionSeeder.php`:
```php
// Feedback Management
['name' => 'View Feedback', 'slug' => 'feedback.view', 'module' => 'feedback'],
['name' => 'Approve Feedback', 'slug' => 'feedback.approve', 'module' => 'feedback'],
['name' => 'Reject Feedback', 'slug' => 'feedback.reject', 'module' => 'feedback'],
['name' => 'Delete Feedback', 'slug' => 'feedback.delete', 'module' => 'feedback'],
['name' => 'Feature Feedback', 'slug' => 'feedback.feature', 'module' => 'feedback'],
```

---

## Navigation

### Admin Menu
```php
// Location: resources/views/layouts/admin.blade.php
// Section: Content Management

@if(auth()->user()->hasPermission('feedback.view'))
<a href="{{ route('admin.feedback.index') }}" 
   class="flex items-center px-4 py-3 text-sm font-medium rounded-lg {{ request()->routeIs('admin.feedback.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:bg-gray-50' }}">
    <i class="fas fa-comments w-5 mr-3"></i>
    <span>Feedback</span>
    @if($pendingCount = \App\Models\Feedback::pending()->count())
        <span class="ml-auto bg-orange-500 text-white text-xs px-2 py-1 rounded-full">{{ $pendingCount }}</span>
    @endif
</a>
@endif
```

### Footer Link (Optional)
```php
// Location: resources/views/components/frontend/footer.blade.php
<a href="{{ route('feedback.index') }}" class="text-gray-400 hover:text-white">
    Customer Feedback
</a>
```

---

## Implementation Checklist

- [x] Create Feedback model
- [x] Create migration
- [x] Create Livewire components (FeedbackForm, FeedbackList, Admin/FeedbackTable)
- [ ] Implement FeedbackForm component logic
- [ ] Implement FeedbackList component logic
- [ ] Implement Admin/FeedbackTable component logic
- [ ] Create FeedbackService
- [ ] Create FeedbackController (Frontend)
- [ ] Create Admin/FeedbackController
- [ ] Create frontend views
- [ ] Create admin views
- [ ] Add routes
- [ ] Add permissions to seeder
- [ ] Update admin navigation
- [ ] Add image compression support
- [ ] Implement auto-user registration
- [ ] Test submission flow
- [ ] Test admin approval flow
- [ ] Test image upload
- [ ] Test infinite scroll

---

## Testing Scenarios

### 1. New Customer Submission
1. Go to feedback page
2. Fill form with new email/mobile
3. Upload images
4. Submit feedback
5. Verify auto-registration
6. Verify auto-login
7. Verify pending status

### 2. Existing Customer Submission
1. Login as existing customer
2. Submit feedback
3. Verify linked to user_id
4. Verify no duplicate user created

### 3. Admin Approval
1. Login as admin
2. View pending feedback
3. Approve feedback
4. Verify status changed
5. Verify appears on frontend

### 4. Featured Feedback
1. Admin marks feedback as featured
2. Check author profile page
3. Verify appears in 60% section
4. Verify "View More" works

### 5. Image Upload
1. Upload multiple images
2. Verify webp conversion
3. Verify thumbnail generation
4. Verify lightbox works

---

## API Responses

### Success Response
```json
{
    "success": true,
    "message": "Thank you for your feedback! It will be reviewed shortly.",
    "data": {
        "feedback_id": 123,
        "user_created": true,
        "auto_login": true
    }
}
```

### Error Response
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "customer_email": ["The email field is required."],
        "rating": ["Please select a rating."]
    }
}
```

---

## Notes
- Feedback is soft-deleted for data retention
- Admin can restore deleted feedback
- Featured feedback limited to 6 items
- Email notifications can be added later
- SMS notifications for feedback status (optional)
- Analytics dashboard for feedback metrics (future enhancement)

---

## Dependencies
- Livewire 3.x
- Laravel 11.x
- Intervention Image (for webp compression)
- Alpine.js (for frontend interactions)
- Tailwind CSS (for styling)
