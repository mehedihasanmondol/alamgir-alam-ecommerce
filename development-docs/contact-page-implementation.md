# Contact Page Implementation Documentation

## Overview
Comprehensive contact page system with Google Maps integration, interactive contact form, chamber information display, and collapsible FAQ section. Fully manageable through admin panel.

---

## Features Implemented

### Frontend Features
1. **Modern UI/UX Design**
   - Gradient header with engaging visuals
   - Three-column responsive layout
   - Card-based information display
   - Mobile-optimized design

2. **Contact Information Card**
   - Email with mailto link
   - Phone with tel link
   - WhatsApp integration with wa.me link
   - Full business address
   - Business hours display
   - Social media links (Facebook, Twitter, Instagram, LinkedIn, YouTube)

3. **Interactive Contact Form (Livewire)**
   - Real-time validation with visual feedback
   - Rate limiting (3 attempts per 5 minutes per IP)
   - Loading states with spinner
   - Success/error message display
   - Character counter for message field
   - Auto-reset on successful submission
   - Fields: Name, Email, Phone (optional), Subject, Message

4. **Google Maps Integration**
   - Embedded interactive map
   - Configurable location via settings
   - Adjustable zoom level
   - Custom marker with company name

5. **Chamber/Corporate Office Information**
   - Separate card for additional office details
   - Title, address, phone, email, hours
   - Optional display (only shows if configured)

6. **FAQ Section with Collapsible Accordion**
   - Alpine.js powered smooth animations
   - Click to expand/collapse
   - Icon rotation on toggle
   - Mobile-friendly touch interactions
   - Ordered display based on admin settings

### Admin Panel Features

#### 1. Contact Settings Management
**Route:** `/admin/contact/settings`

**Settings Groups:**
- **General Information**
  - Company name
  - Email address
  - Phone number
  - WhatsApp number
  - Physical address (street, city, state, zip, country)
  - Business hours

- **Map Settings**
  - Latitude coordinate
  - Longitude coordinate
  - Zoom level

- **Social Media**
  - Facebook URL
  - Twitter URL
  - Instagram URL
  - LinkedIn URL
  - YouTube URL

- **Chamber Information**
  - Chamber/Office title
  - Chamber address
  - Chamber phone
  - Chamber email
  - Chamber hours

**Features:**
- Grouped settings display
- Text, textarea, email, tel, url input types
- Field descriptions for admin guidance
- Cached for performance (1 hour TTL)
- Auto-cache clearing on save
- Google Maps setup instructions

#### 2. FAQ Management
**Routes:**
- List: `/admin/contact/faqs`
- Create: `/admin/contact/faqs/create`
- Edit: `/admin/contact/faqs/{id}/edit`
- Delete: `/admin/contact/faqs/{id}` (DELETE)

**Features:**
- Question and answer fields
- Display order control
- Active/inactive status toggle
- Full CRUD operations
- Pagination (20 per page)
- Visible status badges

#### 3. Contact Messages Management
**Routes:**
- List: `/admin/contact/messages`
- View: `/admin/contact/messages/{id}`
- Update Status: `/admin/contact/messages/{id}/status`
- Delete: `/admin/contact/messages/{id}` (DELETE)
- Bulk Actions: `/admin/contact/messages/bulk-action`

**Features:**
- Unread message counter in header
- Status filtering (Unread, Read, Replied, Archived)
- Search by name, email, subject, or message
- Visual indicators for unread messages (bold text, light background)
- Detailed message view with all submitted information
- Status management (Unread → Read → Replied → Archived)
- Internal admin notes for each message
- IP address and user agent tracking
- Quick actions (Email reply, Call button)
- Bulk operations support
- Message deletion with confirmation
- Pagination (20 per page)

---

## Database Structure

### Tables Created

#### 1. `contact_settings`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| key | string(255) | Unique setting key |
| group | string(255) | Setting group (general, map, social, chamber) |
| value | text | Setting value |
| type | string(255) | Input type (text, textarea, email, tel, url, boolean) |
| description | text | Admin help text |
| order | integer | Display order within group |
| is_active | boolean | Active status |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Last update time |

**Indexes:**
- `key` (unique)
- `group, is_active` (composite)

#### 2. `contact_faqs`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| question | string(255) | FAQ question |
| answer | text | FAQ answer |
| order | integer | Display order |
| is_active | boolean | Active status |
| created_at | timestamp | Creation time |
| updated_at | timestamp | Last update time |

**Indexes:**
- `is_active, order` (composite)

#### 3. `contact_messages`
| Column | Type | Description |
|--------|------|-------------|
| id | bigint unsigned | Primary key |
| name | string(255) | Sender name |
| email | string(255) | Sender email |
| phone | string(20) | Sender phone (nullable) |
| subject | string(255) | Message subject |
| message | text | Message content |
| ip_address | string(255) | Sender IP address |
| user_agent | string(255) | Sender user agent |
| status | enum | Message status (unread, read, replied, archived) |
| admin_note | text | Internal admin note |
| read_at | timestamp | Time marked as read |
| created_at | timestamp | Submission time |
| updated_at | timestamp | Last update time |

**Indexes:**
- `status, created_at` (composite)
- `email`

---

## File Structure

```
app/
├── Models/
│   ├── ContactSetting.php       # Contact settings model with cache
│   ├── ContactFaq.php           # FAQ model with scopes
│   └── ContactMessage.php       # Contact message model with status methods
├── Modules/Contact/
│   ├── Controllers/
│   │   ├── ContactController.php                      # Frontend controller
│   │   └── Admin/
│   │       ├── ContactSettingController.php           # Settings management
│   │       ├── ContactFaqController.php               # FAQ CRUD
│   │       └── ContactMessageController.php           # Message management
│   ├── Services/
│   │   └── ContactService.php                         # Business logic
│   └── Requests/
│       └── ContactMessageRequest.php                   # Form validation
├── Livewire/Contact/
│   └── ContactForm.php                                # Interactive form component

database/
├── migrations/
│   ├── 2025_11_26_131108_create_contact_settings_table.php
│   ├── 2025_11_26_131108_create_contact_faqs_table.php
│   └── 2025_11_26_131112_create_contact_messages_table.php
└── seeders/
    └── ContactSeeder.php                              # Default settings & FAQs

resources/views/
├── frontend/contact/
│   └── index.blade.php                                # Main contact page
├── livewire/contact/
│   └── contact-form.blade.php                         # Form component view
└── admin/contact/
    ├── settings/
    │   └── index.blade.php                            # Settings editor
    ├── faqs/
    │   ├── index.blade.php                            # FAQ list
    │   ├── create.blade.php                           # Create FAQ
    │   └── edit.blade.php                             # Edit FAQ
    └── messages/
        ├── index.blade.php                            # Message list
        └── show.blade.php                             # Message detail

config/
└── services.php                                       # Google Maps API key
```

---

## Routes

### Public Routes
```php
GET  /contact              # Display contact page
POST /contact              # Submit contact form (alternative to Livewire)
```

### Admin Routes (requires authentication)
```php
# Settings
GET  /admin/contact/settings          # View/edit settings
PUT  /admin/contact/settings          # Update settings

# FAQs
GET    /admin/contact/faqs            # List FAQs
GET    /admin/contact/faqs/create     # Create form
POST   /admin/contact/faqs            # Store FAQ
GET    /admin/contact/faqs/{id}/edit  # Edit form
PUT    /admin/contact/faqs/{id}       # Update FAQ
DELETE /admin/contact/faqs/{id}       # Delete FAQ

# Messages
GET    /admin/contact/messages                # List messages
GET    /admin/contact/messages/{id}           # View message
PUT    /admin/contact/messages/{id}/status    # Update status
DELETE /admin/contact/messages/{id}           # Delete message
POST   /admin/contact/messages/bulk-action    # Bulk operations
```

---

## Setup Instructions

### 1. Database Setup
Migrations are already run. If you need to re-run:
```bash
php artisan migrate --path=database/migrations/2025_11_26_131108_create_contact_settings_table.php
php artisan migrate --path=database/migrations/2025_11_26_131108_create_contact_faqs_table.php
php artisan migrate --path=database/migrations/2025_11_26_131112_create_contact_messages_table.php
```

### 2. Seed Default Data
```bash
php artisan db:seed --class=ContactSeeder
```

This creates:
- 23 default contact settings (can be customized in admin)
- 8 sample FAQs (can be edited/deleted in admin)

### 3. Google Maps API Key Setup

#### Get API Key:
1. Go to [Google Cloud Console](https://console.cloud.google.com/google/maps-apis)
2. Create a new project or select existing
3. Enable "Maps JavaScript API"
4. Create credentials → API Key
5. Restrict API key (recommended):
   - Application restrictions: HTTP referrers
   - API restrictions: Maps JavaScript API

#### Configure in Laravel:
Add to `.env`:
```env
GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here
```

The config is already added to `config/services.php`:
```php
'google_maps' => [
    'api_key' => env('GOOGLE_MAPS_API_KEY'),
],
```

### 4. Update Contact Settings
After setup, login to admin panel:
1. Navigate to `/admin/contact/settings`
2. Update all placeholders with actual business information
3. Set correct map coordinates (use [LatLong.net](https://www.latlong.net/) to find your location)
4. Add social media URLs
5. Configure chamber information if applicable

### 5. Customize FAQs
1. Navigate to `/admin/contact/faqs`
2. Edit default FAQs to match your business
3. Add additional FAQs as needed
4. Adjust display order
5. Toggle active/inactive status

---

## Usage Examples

### Get Contact Setting in Code
```php
use App\Models\ContactSetting;

// Get single setting
$email = ContactSetting::get('email', 'default@example.com');

// Get all settings in a group
$socialMedia = ContactSetting::getGroup('social');
```

### Service Layer Usage
```php
use App\Modules\Contact\Services\ContactService;

$contactService = new ContactService();

// Get all settings
$settings = $contactService->getContactSettings();

// Get chamber info
$chamber = $contactService->getChamberInfo();

// Get active FAQs
$faqs = $contactService->getActiveFaqs();

// Store message
$message = $contactService->storeMessage($data);

// Update message status
$message = $contactService->updateMessageStatus($message, 'replied', 'Responded via email');
```

---

## Security Features

1. **Rate Limiting**
   - 3 form submissions per 5 minutes per IP address
   - Prevents spam and abuse

2. **CSRF Protection**
   - All forms protected with Laravel CSRF tokens

3. **Input Validation**
   - Server-side validation on all inputs
   - Real-time client-side validation
   - XSS protection via Laravel escaping

4. **IP Tracking**
   - Automatic IP address logging
   - User agent tracking for security analysis

5. **Admin-Only Access**
   - Settings management requires authentication
   - Message viewing requires admin role

---

## Performance Optimizations

1. **Settings Caching**
   - All contact settings cached for 1 hour
   - Auto-invalidation on updates
   - Reduces database queries

2. **Lazy Loading**
   - Google Maps loaded asynchronously
   - Form submission via Livewire (no page reload)

3. **Query Optimization**
   - Indexed database fields
   - Scoped queries for active FAQs
   - Pagination on admin lists

4. **Asset Optimization**
   - Tailwind CSS (compiled, not CDN)
   - Alpine.js (local, not CDN)
   - SVG icons (no external requests)

---

## Best Practices

### For Admins
1. **Keep Settings Updated**
   - Regularly review and update contact information
   - Test email links and phone numbers
   - Verify map location accuracy

2. **Manage Messages Promptly**
   - Check unread messages daily
   - Update status as you process
   - Use admin notes to track follow-ups
   - Archive old messages to keep inbox clean

3. **Maintain FAQs**
   - Add FAQs based on common questions
   - Keep answers concise and helpful
   - Update order to prioritize important questions
   - Deactivate outdated FAQs instead of deleting

### For Developers
1. **Rate Limiting**
   - Adjust limits in `ContactForm.php` if needed
   - Monitor for legitimate users being blocked

2. **Email Integration**
   - Consider adding auto-reply functionality
   - Set up email notifications for new messages
   - Integrate with CRM for message tracking

3. **Customization**
   - Extend `ContactMessage` model for additional fields
   - Add custom validation rules in `ContactMessageRequest`
   - Customize email templates

---

## Troubleshooting

### Google Maps Not Showing
1. Check API key is set in `.env`
2. Verify API is enabled in Google Cloud Console
3. Check browser console for errors
4. Ensure latitude/longitude are set in settings

### Contact Form Not Submitting
1. Check rate limiting hasn't been triggered
2. Verify all required fields are filled
3. Check browser console for Livewire errors
4. Ensure CSRF token is present

### Messages Not Appearing in Admin
1. Check database connection
2. Verify migrations have run
3. Check user permissions
4. Look for JavaScript errors in console

### Settings Not Saving
1. Verify CSRF token
2. Check form validation errors
3. Ensure user has admin role
4. Check file permissions for cache

---

## Future Enhancements

### Potential Features
1. **Email Notifications**
   - Auto-email to admin on new message
   - Auto-reply to user confirming receipt
   - Weekly digest of pending messages

2. **Advanced Analytics**
   - Message volume charts
   - Response time tracking
   - Popular FAQ analytics
   - Geographic distribution of messages

3. **Multi-Language Support**
   - Translate FAQs
   - Multi-language contact forms
   - Language-specific settings

4. **File Attachments**
   - Allow file uploads with messages
   - File type validation
   - Virus scanning

5. **Chat Integration**
   - Live chat widget
   - Chatbot for FAQ automation
   - WhatsApp Business API integration

6. **Appointment Booking**
   - Calendar integration
   - Time slot selection
   - Automated reminders

---

## Testing Checklist

### Frontend Testing
- [ ] Contact form submits successfully
- [ ] Real-time validation works
- [ ] Success/error messages display correctly
- [ ] Google Maps loads and shows correct location
- [ ] FAQs expand/collapse smoothly
- [ ] Social media links work
- [ ] Mobile responsive design
- [ ] Rate limiting prevents spam

### Admin Testing
- [ ] Settings can be updated
- [ ] FAQs can be created/edited/deleted
- [ ] Messages display correctly
- [ ] Status updates work
- [ ] Bulk actions function
- [ ] Search and filtering work
- [ ] Pagination works
- [ ] Delete confirmations appear

---

## Changelog

### Version 1.0.0 (2025-11-26)
- Initial implementation
- Contact page with Google Maps
- Interactive Livewire contact form
- FAQ section with Alpine.js accordions
- Admin settings management
- FAQ management system
- Message inbox with status tracking
- Smart seeder with upsert logic
- Complete documentation

---

## Support & Maintenance

### Regular Maintenance Tasks
1. **Weekly**
   - Review and respond to messages
   - Update message statuses
   - Check for spam submissions

2. **Monthly**
   - Review and update FAQs
   - Verify contact information accuracy
   - Check Google Maps functionality

3. **Quarterly**
   - Audit archived messages
   - Update business hours if changed
   - Review rate limiting effectiveness

### Getting Help
For issues or questions about this implementation:
1. Check this documentation first
2. Review Laravel and Livewire documentation
3. Check Google Maps API documentation for map issues
4. Contact development team for custom modifications

---

**Documentation Last Updated:** November 26, 2025
**Implementation Version:** 1.0.0
**Laravel Version:** 11.x
**Livewire Version:** 3.x
