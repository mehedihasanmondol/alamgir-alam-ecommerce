php artisan migrate --path=database/migrations/2025_11_25_150714_create_system_settings_table.php

php artisan db:seed --class=SystemSettingSeeder

php artisan db:seed --class=RolePermissionSeeder

php artisan migrate --path=database/migrations/2025_11_25_162223_create_feedback_table.php

php artisan db:seed --class=RolePermissionSeeder

 php artisan migrate --path=database/migrations/2025_11_26_000001_create_feedback_votes_table.php

# Seed feedback settings
php artisan db:seed --class=SiteSettingSeeder

# Fix users email unique constraint
php artisan migrate --path=database/migrations/2025_11_26_071500_modify_users_email_unique.php

# Clear caches
php artisan view:clear
php artisan config:clear
php artisan cache:clear



--------------------------------



php artisan migrate --path=database/migrations/2025_11_26_025300_create_chambers_table.php

php artisan migrate --path=database/migrations/2025_11_26_025301_create_appointments_table.php

php artisan db:seed --class=RolePermissionSeeder
php artisan db:seed --class=SiteSettingSeeder
php artisan db:seed --class=ChamberSeeder

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# ✅ APPOINTMENT SYSTEM COMPLETE!
# Admin: /admin/appointments
# Frontend: Author profile pages (responsive layout)

# ✅ FEEDBACK ENHANCEMENTS COMPLETE!
# - Customizable feedback title (Site Settings > Feedback)
# - Time display toggle (Site Settings > Feedback)
# - Helpful/Not Helpful voting toggle (Site Settings > Feedback)
# - Full width when appointments disabled
# - No "Coming Soon" content when appointments off
# - Settings work on both author profile AND /feedback page

# ✅ AUTHOR PROFILE WHATSAPP ADDED!
# - WhatsApp social link added to author profiles
# - Shows with other social links (Twitter, Facebook, etc.)
# - Admin can add WhatsApp number in user create/edit forms
# - Format: Include country code (e.g., 8801712345678)
# - FIXED: Added 'whatsapp' to AuthorProfile $fillable array

# ✅ ADMIN HEADER PROFILE IMAGE FIXED!
# - Now uses media library avatar (auth()->user()->media->small_url)
# - Matches frontend header implementation exactly
# - Fallback to direct upload avatar if no media library image
# - Fallback to initials if no avatar at all

# ✅ CUSTOMER PANEL APPOINTMENTS & FEEDBACK ADDED!
# - My Appointments link in customer SIDEBAR (if appointments enabled)
# - My Feedback link in customer SIDEBAR (if feedback enabled)
# - REMOVED from frontend header dropdown (only in sidebar)
# - Routes added: /my/appointments, /my/feedback
# - Controllers created: Customer\AppointmentController, Customer\FeedbackController
# - Views created: appointments/index, appointments/show, feedback/index, feedback/show
# - Features: View, show details, cancel appointments
# - Features: View, edit, delete feedback
# - FIXED: Changed customer_id to user_id (matches appointments table schema)
# - Sidebar links added to both mobile and desktop customer sidebar
# - Product-style modals: Cancel appointments & delete feedback (backdrop blur)
# - Feedback title field REMOVED from all forms
# - Rating field conditional based on 'feedback_rating_enabled' setting




php artisan migrate --path=database/migrations/2025_11_26_055411_add_whatsapp_to_author_profiles_table.php




---------------------

php artisan migrate --path=database/migrations/2025_11_26_131108_create_contact_settings_table.php

php artisan migrate --path=database/migrations/2025_11_26_131108_create_contact_faqs_table.php

php artisan migrate --path=database/migrations/2025_11_26_131112_create_contact_messages_table.php

php artisan db:seed --class=ContactSeeder
-------------------------

php artisan db:seed --class=SiteSettingSeeder

-------------------------
# November 27, 2025 - Site Settings & Profile Fixes
# Fixed: Author page & sitemap dropdown options now display correctly
# Added: Admin profile page at /admin/profile
# Modified: Sitemap options format, added select field parsing
# Run seeder to update sitemap options format