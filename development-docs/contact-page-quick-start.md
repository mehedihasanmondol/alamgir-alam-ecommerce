# Contact Page - Quick Start Guide

## ✅ What's Been Implemented

### Frontend Features
- ✅ Modern contact page with gradient header at `/contact`
- ✅ Interactive Livewire contact form with real-time validation
- ✅ Google Maps integration (configurable location)
- ✅ Contact information card (email, phone, WhatsApp, address, hours)
- ✅ Social media links (Facebook, Twitter, Instagram, LinkedIn, YouTube)
- ✅ Chamber/corporate office information card
- ✅ Collapsible FAQ section with Alpine.js animations
- ✅ Fully mobile responsive design

### Admin Features
- ✅ Contact settings management at `/admin/contact/settings`
- ✅ FAQ management (CRUD) at `/admin/contact/faqs`
- ✅ Message inbox with status tracking at `/admin/contact/messages`
- ✅ Message filtering and search
- ✅ Bulk actions on messages
- ✅ Internal admin notes

### Database & Backend
- ✅ 3 database tables created (contact_settings, contact_faqs, contact_messages)
- ✅ All migrations run successfully
- ✅ Default data seeded (23 settings + 8 FAQs)
- ✅ Service layer following project architecture
- ✅ Proper validation and security measures

---

## 🚀 Next Steps (Required)

### 1. Get Google Maps API Key (5 minutes)
```bash
# 1. Go to: https://console.cloud.google.com/google/maps-apis
# 2. Create project or select existing
# 3. Enable "Maps JavaScript API"
# 4. Create credentials → API Key
# 5. Add to .env file:
```

Add this line to your `.env` file:
```env
GOOGLE_MAPS_API_KEY=your_actual_api_key_here
```

### 2. Update Contact Information (10 minutes)
1. Login to admin panel
2. Go to `/admin/contact/settings`
3. Update ALL placeholder values with your actual business information:
   - Company name
   - Email address
   - Phone numbers
   - Physical address
   - Business hours
   - **Map coordinates** (use https://www.latlong.net/ to find yours)
   - Social media URLs
   - Chamber/office information

### 3. Customize FAQs (5 minutes)
1. Go to `/admin/contact/faqs`
2. Edit the 8 default FAQs to match your business
3. Add more FAQs as needed
4. Adjust display order
5. Activate/deactivate as needed

---

## 📍 How to Find Your Map Coordinates

**Option 1: Google Maps**
1. Open Google Maps
2. Right-click on your business location
3. Click the coordinates that appear
4. Copy latitude and longitude

**Option 2: LatLong.net**
1. Go to https://www.latlong.net/
2. Enter your address
3. Copy the coordinates
4. Paste into admin settings

**Format:**
- Latitude: 23.8103 (for Dhaka)
- Longitude: 90.4125 (for Dhaka)

---

## 🎯 Quick Test Checklist

### Frontend Test
1. Visit `/contact` on your site
2. Check if Google Maps shows (if API key is set)
3. Fill out and submit the contact form
4. Verify success message appears
5. Expand/collapse FAQs

### Admin Test
1. Login to admin panel
2. Visit `/admin/contact/messages`
3. Check if your test message appears
4. Update message status
5. Visit `/admin/contact/settings` and verify all settings display
6. Visit `/admin/contact/faqs` and verify FAQs list

---

## 📧 Contact Form Features

### Security
- ✅ Rate limiting: 3 submissions per 5 minutes per IP
- ✅ CSRF protection
- ✅ Input validation
- ✅ XSS protection
- ✅ IP address tracking

### User Experience
- ✅ Real-time validation
- ✅ Loading spinner on submit
- ✅ Character counter (5000 max)
- ✅ Success/error messages
- ✅ Form auto-reset on success
- ✅ Mobile responsive

---

## 🔗 Important URLs

### Public
- Contact Page: `http://yoursite.com/contact`

### Admin
- Settings: `http://yoursite.com/admin/contact/settings`
- FAQs: `http://yoursite.com/admin/contact/faqs`
- Messages: `http://yoursite.com/admin/contact/messages`

---

## 📝 Default Settings Created

The seeder has created these settings (all editable in admin):

**General (10 settings)**
- company_name, email, phone, whatsapp
- address, city, state, zip, country
- business_hours

**Map (3 settings)**
- map_latitude (23.8103 - Dhaka default)
- map_longitude (90.4125 - Dhaka default)
- map_zoom (15 default)

**Social (5 settings)**
- facebook, twitter, instagram, linkedin, youtube

**Chamber (5 settings)**
- chamber_title, chamber_address
- chamber_phone, chamber_email, chamber_hours

---

## 🎨 Customization Options

### Change Colors
Edit `/resources/views/frontend/contact/index.blade.php`:
- Header gradient: `bg-gradient-to-r from-blue-600 to-blue-800`
- Buttons: `bg-blue-600 hover:bg-blue-700`
- Badges: `bg-gradient-success`, `bg-gradient-info`, etc.

### Add More FAQs
Use admin panel at `/admin/contact/faqs` or add to seeder.

### Modify Form Fields
Edit `/app/Livewire/Contact/ContactForm.php` for backend.
Edit `/resources/views/livewire/contact/contact-form.blade.php` for frontend.

---

## 🐛 Troubleshooting

### Map Not Showing?
- ✅ Check if GOOGLE_MAPS_API_KEY is in .env
- ✅ Verify API is enabled in Google Cloud Console
- ✅ Check browser console for errors
- ✅ Ensure coordinates are set in admin settings

### Form Not Submitting?
- ✅ Check browser console for errors
- ✅ Verify you haven't hit rate limit (3 per 5 min)
- ✅ Check all required fields are filled
- ✅ Clear browser cache

### Settings Not Saving?
- ✅ Check if logged in as admin
- ✅ Clear cache: `php artisan cache:clear`
- ✅ Check browser console for errors

---

## 📚 Full Documentation
See `development-docs/contact-page-implementation.md` for complete technical documentation.

---

## ✨ Summary

**You now have:**
- ✅ Fully functional contact page
- ✅ Admin panel to manage everything
- ✅ 23 configurable settings
- ✅ 8 default FAQs (customizable)
- ✅ Message inbox system
- ✅ Google Maps integration (needs API key)
- ✅ Rate limiting and security
- ✅ Mobile responsive design

**Just need to:**
1. Add Google Maps API key to .env
2. Update contact information in admin
3. Customize FAQs for your business

That's it! Your contact page is ready to go! 🚀
