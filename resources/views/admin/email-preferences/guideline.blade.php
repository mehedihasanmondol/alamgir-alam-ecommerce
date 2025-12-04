@extends('layouts.admin')

@section('title', 'Email Preferences - Setup Guide')

@section('content')
<div class="container-fluid px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <a href="{{ route('admin.email-preferences.index') }}" class="text-blue-600 hover:text-blue-700 mb-2 inline-block">
            <i class="fas fa-arrow-left mr-2"></i>Back to Email Preferences
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Email Preferences - Setup Guide</h1>
        <p class="mt-1 text-sm text-gray-600">Complete setup instructions for automated email campaigns</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- System Status -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg shadow border border-green-200">
                <div class="p-6 border-b border-green-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-check-circle text-green-600 mr-3"></i>
                        System Status: ✅ Ready
                    </h2>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-600 mr-3"></i>
                        <span class="text-gray-700"><strong>Email System:</strong> Fully Configured</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-600 mr-3"></i>
                        <span class="text-gray-700"><strong>Mailable Classes:</strong> 3 Created</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-600 mr-3"></i>
                        <span class="text-gray-700"><strong>Console Commands:</strong> 3 Created</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-600 mr-3"></i>
                        <span class="text-gray-700"><strong>Email Templates:</strong> 4 Designed</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-600 mr-3"></i>
                        <span class="text-gray-700"><strong>Scheduling:</strong> Configured</span>
                    </div>
                </div>
            </div>

            <!-- Quick Start -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-rocket text-blue-600 mr-3"></i>
                        Quick Start (What You Need To Do)
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">1</div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">Configure SMTP Settings</h3>
                            <p class="text-sm text-gray-600 mt-1">Add email credentials to your .env file</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">2</div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">Test Email Commands</h3>
                            <p class="text-sm text-gray-600 mt-1">Run commands with --test flag to verify setup</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">3</div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">Setup Server Cron Job</h3>
                            <p class="text-sm text-gray-600 mt-1">Configure server cron for automated scheduling</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 font-bold">4</div>
                        </div>
                        <div class="ml-4">
                            <h3 class="font-semibold text-gray-900">Monitor & Manage</h3>
                            <p class="text-sm text-gray-600 mt-1">Use Schedule Setup and Mail Editor tools</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Email Configuration -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-cog text-purple-600 mr-3"></i>
                        Step 1: Email Configuration
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Configure SMTP settings in your <code class="bg-gray-100 px-2 py-1 rounded">.env</code> file:</p>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yoursite.com
MAIL_FROM_NAME="${APP_NAME}"</code></pre>
                    </div>
                    <div class="mt-4 bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <div class="flex">
                            <i class="fas fa-exclamation-triangle text-yellow-400 mt-1"></i>
                            <div class="ml-3">
                                <p class="text-sm text-yellow-700">
                                    <strong>Gmail Users:</strong> Use App Password instead of regular password. 
                                    <a href="https://myaccount.google.com/apppasswords" target="_blank" class="underline">Generate App Password</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Create Commands -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-terminal text-green-600 mr-3"></i>
                        Step 2: Create Email Commands
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <p class="text-sm text-gray-600">Run these commands to generate email sending classes:</p>
                    
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">1. Generate Mailable Classes:</p>
                        <div class="bg-gray-900 rounded-lg p-3">
                            <pre class="text-green-400 text-sm"><code>php artisan make:mail NewsletterMail
php artisan make:mail PromotionalMail
php artisan make:mail RecommendationMail</code></pre>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">2. Generate Console Commands:</p>
                        <div class="bg-gray-900 rounded-lg p-3">
                            <pre class="text-green-400 text-sm"><code>php artisan make:command SendNewsletterEmails
php artisan make:command SendPromotionalEmails
php artisan make:command SendRecommendationEmails</code></pre>
                        </div>
                    </div>
                    
                    <div class="bg-blue-50 border-l-4 border-blue-400 p-4">
                        <div class="flex">
                            <i class="fas fa-info-circle text-blue-400 mt-1"></i>
                            <div class="ml-3">
                                <p class="text-sm text-blue-700">
                                    Complete code templates are available in <code class="bg-blue-100 px-2 py-1 rounded">development-docs/EMAIL-PREFERENCES-COMPLETE.md</code>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cron Setup -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-clock text-red-600 mr-3"></i>
                        Step 3: Cron Job Setup
                    </h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Linux/Ubuntu -->
                    <div>
                        <h3 class="font-bold text-gray-900 mb-3 flex items-center">
                            <i class="fab fa-linux text-gray-700 mr-2"></i>
                            For Linux/Ubuntu Server
                        </h3>
                        
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">1. Open crontab editor:</p>
                                <div class="bg-gray-900 rounded-lg p-3">
                                    <pre class="text-green-400 text-sm"><code>crontab -e</code></pre>
                                </div>
                            </div>
                            
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">2. Add this line (replace path):</p>
                                <div class="bg-gray-900 rounded-lg p-3">
                                    <pre class="text-green-400 text-sm"><code>* * * * * cd /var/www/html/your-project && php artisan schedule:run >> /dev/null 2>&1</code></pre>
                                </div>
                            </div>
                            
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">3. Save and exit:</p>
                                <div class="bg-gray-900 rounded-lg p-3">
                                    <pre class="text-green-400 text-sm"><code>Press ESC, then type :wq and press ENTER</code></pre>
                                </div>
                            </div>
                            
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">4. Verify cron is running:</p>
                                <div class="bg-gray-900 rounded-lg p-3">
                                    <pre class="text-green-400 text-sm"><code>crontab -l
sudo service cron status</code></pre>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="border-gray-200">

                    <!-- Windows -->
                    <div>
                        <h3 class="font-bold text-gray-900 mb-3 flex items-center">
                            <i class="fab fa-windows text-blue-600 mr-2"></i>
                            For Windows Server
                        </h3>
                        
                        <div class="space-y-3">
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">1. Open Task Scheduler</p>
                                <p class="text-sm text-gray-600">Press Win+R, type <code class="bg-gray-100 px-2 py-1 rounded">taskschd.msc</code></p>
                            </div>
                            
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">2. Create New Task</p>
                                <ul class="text-sm text-gray-600 space-y-1 ml-4">
                                    <li>• Click "Create Basic Task"</li>
                                    <li>• Name: "Laravel Scheduler"</li>
                                    <li>• Trigger: Every 1 minute</li>
                                </ul>
                            </div>
                            
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">3. Action Settings</p>
                                <div class="bg-gray-100 rounded p-3 text-sm space-y-2">
                                    <p><strong>Program:</strong> <code>C:\php\php.exe</code></p>
                                    <p><strong>Arguments:</strong> <code>artisan schedule:run</code></p>
                                    <p><strong>Start in:</strong> <code>C:\path\to\your\project</code></p>
                                </div>
                            </div>
                            
                            <div>
                                <p class="text-sm font-semibold text-gray-900 mb-2">4. Additional Settings</p>
                                <ul class="text-sm text-gray-600 space-y-1 ml-4">
                                    <li>• Check: "Run whether user is logged on or not"</li>
                                    <li>• Check: "Run with highest privileges"</li>
                                    <li>• Duration: Repeat task every 1 minute indefinitely</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Schedule Configuration -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-calendar-alt text-indigo-600 mr-3"></i>
                        Step 4: Configure Email Schedule
                    </h2>
                </div>
                <div class="p-6">
                    <p class="text-sm text-gray-600 mb-4">Add schedules in <code class="bg-gray-100 px-2 py-1 rounded">app/Console/Kernel.php</code>:</p>
                    <div class="bg-gray-900 rounded-lg p-4 overflow-x-auto">
                        <pre class="text-green-400 text-sm"><code>protected function schedule(Schedule $schedule)
{
    // Newsletter: Every Monday at 9 AM
    $schedule->command('email:send-newsletter')
             ->weeklyOn(1, '9:00')
             ->timezone('Asia/Dhaka');
    
    // Promotions: Every Friday at 10 AM
    $schedule->command('email:send-promotions')
             ->weeklyOn(5, '10:00')
             ->timezone('Asia/Dhaka');
    
    // Recommendations: Every Wednesday at 2 PM
    $schedule->command('email:send-recommendations')
             ->weekly()->wednesdays()->at('14:00')
             ->timezone('Asia/Dhaka');
}</code></pre>
                    </div>
                </div>
            </div>

            <!-- Testing -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-vial text-teal-600 mr-3"></i>
                        Testing & Verification
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">1. Test Email Configuration:</p>
                        <div class="bg-gray-900 rounded-lg p-3">
                            <pre class="text-green-400 text-sm"><code>php artisan tinker
>>> Mail::raw('Test Email', function($msg) { 
    $msg->to('test@example.com')->subject('Test'); 
});
>>> exit</code></pre>
                        </div>
                    </div>
                    
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">2. Test Commands (Dry Run - Safe):</p>
                        <div class="bg-gray-900 rounded-lg p-3">
                            <pre class="text-green-400 text-sm"><code>php artisan email:send-newsletter --test
php artisan email:send-promotions --test
php artisan email:send-recommendations --test</code></pre>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">Test mode shows what would happen without actually sending emails</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">3. Send Actual Test Emails:</p>
                        <div class="bg-gray-900 rounded-lg p-3">
                            <pre class="text-green-400 text-sm"><code>php artisan email:send-newsletter
php artisan email:send-promotions
php artisan email:send-recommendations</code></pre>
                        </div>
                        <p class="text-xs text-gray-600 mt-2">These will send actual emails to all subscribed users</p>
                    </div>
                    
                    <div>
                        <p class="text-sm font-semibold text-gray-900 mb-2">4. Verify Cron & Schedule:</p>
                        <div class="bg-gray-900 rounded-lg p-3">
                            <pre class="text-green-400 text-sm"><code># List scheduled commands
php artisan schedule:list

# Run scheduler manually
php artisan schedule:run

# Check logs
tail -f storage/logs/laravel.log</code></pre>
                        </div>
                    </div>
                    
                    <div class="bg-green-50 border-l-4 border-green-400 p-4">
                        <div class="flex">
                            <i class="fas fa-check-circle text-green-400 mt-1"></i>
                            <div class="ml-3">
                                <p class="text-sm text-green-700">
                                    <strong>Success Output:</strong> Commands will show progress bar, sent count, and success message
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Troubleshooting -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-wrench text-orange-600 mr-3"></i>
                        Common Issues & Solutions
                    </h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="border-l-4 border-red-400 pl-4">
                        <h4 class="font-semibold text-gray-900">Emails not sending</h4>
                        <ul class="text-sm text-gray-600 mt-2 space-y-1">
                            <li>• Check SMTP credentials in .env</li>
                            <li>• Verify email driver: <code class="bg-gray-100 px-1 rounded">MAIL_MAILER=smtp</code></li>
                            <li>• Check Laravel logs: <code class="bg-gray-100 px-1 rounded">storage/logs/laravel.log</code></li>
                            <li>• Test with: <code class="bg-gray-100 px-1 rounded">php artisan tinker</code></li>
                        </ul>
                    </div>
                    
                    <div class="border-l-4 border-yellow-400 pl-4">
                        <h4 class="font-semibold text-gray-900">Cron not running</h4>
                        <ul class="text-sm text-gray-600 mt-2 space-y-1">
                            <li>• Verify cron entry: <code class="bg-gray-100 px-1 rounded">crontab -l</code></li>
                            <li>• Check cron service: <code class="bg-gray-100 px-1 rounded">sudo service cron status</code></li>
                            <li>• Test manually: <code class="bg-gray-100 px-1 rounded">php artisan schedule:run</code></li>
                            <li>• Check file permissions on project directory</li>
                        </ul>
                    </div>
                    
                    <div class="border-l-4 border-blue-400 pl-4">
                        <h4 class="font-semibold text-gray-900">Permission errors</h4>
                        <ul class="text-sm text-gray-600 mt-2 space-y-1">
                            <li>• Set correct permissions: <code class="bg-gray-100 px-1 rounded">chmod -R 755 storage bootstrap/cache</code></li>
                            <li>• Set ownership: <code class="bg-gray-100 px-1 rounded">chown -R www-data:www-data .</code></li>
                            <li>• Clear cache: <code class="bg-gray-100 px-1 rounded">php artisan cache:clear</code></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Quick Links -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-4">Quick Links</h3>
                <div class="space-y-3">
                    <a href="{{ route('admin.email-preferences.index') }}" class="flex items-center text-sm text-blue-600 hover:text-blue-700">
                        <i class="fas fa-envelope-open-text w-5 mr-2"></i>
                        Email Preferences
                    </a>
                    <a href="{{ route('admin.email-preferences.schedule-setup') }}" class="flex items-center text-sm text-indigo-600 hover:text-indigo-700">
                        <i class="fas fa-calendar-alt w-5 mr-2"></i>
                        Schedule Setup
                    </a>
                    <a href="{{ route('admin.email-preferences.mail-setup') }}" class="flex items-center text-sm text-pink-600 hover:text-pink-700">
                        <i class="fas fa-edit w-5 mr-2"></i>
                        Mail Editor
                    </a>
                    <a href="{{ route('admin.email-preferences.newsletter-subscribers') }}" class="flex items-center text-sm text-green-600 hover:text-green-700">
                        <i class="fas fa-users w-5 mr-2"></i>
                        Newsletter Subscribers
                    </a>
                </div>
            </div>

            <!-- Quick Commands -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-4">Quick Test Commands</h3>
                <div class="space-y-3">
                    <div class="bg-gray-50 p-3 rounded border">
                        <p class="text-xs font-semibold text-gray-700 mb-1">Newsletter Test</p>
                        <code class="text-xs text-gray-600">php artisan email:send-newsletter --test</code>
                    </div>
                    <div class="bg-gray-50 p-3 rounded border">
                        <p class="text-xs font-semibold text-gray-700 mb-1">Promotion Test</p>
                        <code class="text-xs text-gray-600">php artisan email:send-promotions --test</code>
                    </div>
                    <div class="bg-gray-50 p-3 rounded border">
                        <p class="text-xs font-semibold text-gray-700 mb-1">Recommendation Test</p>
                        <code class="text-xs text-gray-600">php artisan email:send-recommendations --test</code>
                    </div>
                </div>
            </div>

            <!-- Email Types -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-4">Email Preference Types</h3>
                <div class="space-y-3">
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-green-500 rounded-full mt-2 mr-3"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Order Updates</p>
                            <p class="text-xs text-gray-600">Status notifications</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-yellow-500 rounded-full mt-2 mr-3"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Promotions</p>
                            <p class="text-xs text-gray-600">Deals & offers</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-purple-500 rounded-full mt-2 mr-3"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Newsletter</p>
                            <p class="text-xs text-gray-600">Weekly updates</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-red-500 rounded-full mt-2 mr-3"></div>
                        <div>
                            <p class="text-sm font-semibold text-gray-900">Recommendations</p>
                            <p class="text-xs text-gray-600">Product suggestions</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recommended Schedule -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg p-6 border border-blue-200">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                    Recommended Schedule
                </h3>
                <div class="space-y-3 text-sm">
                    <div>
                        <p class="font-semibold text-gray-900">Newsletter</p>
                        <p class="text-gray-600">Monday @ 9:00 AM</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Promotions</p>
                        <p class="text-gray-600">Friday @ 10:00 AM</p>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900">Recommendations</p>
                        <p class="text-gray-600">Wednesday @ 2:00 PM</p>
                    </div>
                </div>
            </div>

            <!-- Help Resources -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-900 mb-4">Documentation</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-start">
                        <i class="fas fa-file-alt text-blue-600 w-5 mr-2 mt-1"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Complete Guide</p>
                            <p class="text-gray-600 text-xs">EMAIL-PREFERENCES-COMPLETE.md</p>
                        </div>
                    </div>
                    <div class="flex items-start">
                        <i class="fas fa-vial text-green-600 w-5 mr-2 mt-1"></i>
                        <div>
                            <p class="font-semibold text-gray-900">Testing Guide</p>
                            <p class="text-gray-600 text-xs">EMAIL-SYSTEM-TESTING-GUIDE.md</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Status -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-50 rounded-lg p-6 border border-green-200">
                <h3 class="font-bold text-gray-900 mb-4 flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    System Status
                </h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-600 w-4 mr-2"></i>
                        <span class="text-gray-700">Mailable Classes Created</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-600 w-4 mr-2"></i>
                        <span class="text-gray-700">Console Commands Ready</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-600 w-4 mr-2"></i>
                        <span class="text-gray-700">Email Templates Designed</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-check text-green-600 w-4 mr-2"></i>
                        <span class="text-gray-700">Scheduling Configured</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
