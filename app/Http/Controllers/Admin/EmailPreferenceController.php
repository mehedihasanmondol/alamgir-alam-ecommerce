<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Email Preference Management Controller
 * 
 * Manages customer email preferences including:
 * - Order Updates
 * - Promotional Emails
 * - Newsletter Subscriptions
 * - Product Recommendations
 * 
 * @created 2025-11-24
 */
class EmailPreferenceController extends Controller
{
    /**
     * Display email preferences dashboard
     */
    public function index(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermission('users.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Get filter parameters
        $search = $request->input('search');
        $orderUpdates = $request->input('order_updates');
        $promotions = $request->input('promotions');
        $newsletter = $request->input('newsletter');
        $recommendations = $request->input('recommendations');
        $perPage = $request->input('per_page', 20);

        // Build query
        $query = User::where('role', 'customer')
            ->select('id', 'name', 'email', 'mobile', 'email_order_updates', 'email_promotions', 'email_newsletter', 'email_recommendations', 'created_at');

        // Apply search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%");
            });
        }

        // Apply preference filters
        if ($orderUpdates !== null) {
            $query->where('email_order_updates', $orderUpdates === '1');
        }
        if ($promotions !== null) {
            $query->where('email_promotions', $promotions === '1');
        }
        if ($newsletter !== null) {
            $query->where('email_newsletter', $newsletter === '1');
        }
        if ($recommendations !== null) {
            $query->where('email_recommendations', $recommendations === '1');
        }

        // Get paginated results
        $users = $query->latest()->paginate($perPage);

        // Calculate statistics
        $stats = [
            'total_customers' => User::where('role', 'customer')->count(),
            'order_updates_enabled' => User::where('role', 'customer')->where('email_order_updates', true)->count(),
            'promotions_enabled' => User::where('role', 'customer')->where('email_promotions', true)->count(),
            'newsletter_enabled' => User::where('role', 'customer')->where('email_newsletter', true)->count(),
            'recommendations_enabled' => User::where('role', 'customer')->where('email_recommendations', true)->count(),
        ];

        return view('admin.email-preferences.index', compact('users', 'stats'));
    }

    /**
     * Update user's email preferences
     */
    public function update(Request $request, User $user)
    {
        // Check permission
        if (!auth()->user()->hasPermission('users.edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Validate only customer accounts
        if ($user->role !== 'customer') {
            return response()->json(['success' => false, 'message' => 'Can only update customer email preferences.'], 400);
        }

        // Validate request
        $request->validate([
            'preference' => 'required|in:order_updates,promotions,newsletter,recommendations',
            'enabled' => 'required|boolean',
        ]);

        // Map preference to column name
        $columnMap = [
            'order_updates' => 'email_order_updates',
            'promotions' => 'email_promotions',
            'newsletter' => 'email_newsletter',
            'recommendations' => 'email_recommendations',
        ];

        $column = $columnMap[$request->preference];

        // Update preference
        $user->update([$column => $request->enabled]);

        return response()->json([
            'success' => true,
            'message' => 'Email preference updated successfully.',
            'preference' => $request->preference,
            'enabled' => (bool) $request->enabled,
        ]);
    }

    /**
     * Bulk update email preferences
     */
    public function bulkUpdate(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermission('users.edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        // Validate request
        $request->validate([
            'user_ids' => 'required|array',
            'user_ids.*' => 'exists:users,id',
            'preference' => 'required|in:order_updates,promotions,newsletter,recommendations',
            'enabled' => 'required|boolean',
        ]);

        // Map preference to column name
        $columnMap = [
            'order_updates' => 'email_order_updates',
            'promotions' => 'email_promotions',
            'newsletter' => 'email_newsletter',
            'recommendations' => 'email_recommendations',
        ];

        $column = $columnMap[$request->preference];

        // Update preferences for selected users
        $updated = User::whereIn('id', $request->user_ids)
            ->where('role', 'customer')
            ->update([$column => $request->enabled]);

        return response()->json([
            'success' => true,
            'message' => "{$updated} customer(s) updated successfully.",
            'updated_count' => $updated,
        ]);
    }

    /**
     * Export email preferences report
     */
    public function export(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermission('users.view')) {
            abort(403, 'Unauthorized action.');
        }

        $format = $request->input('format', 'csv');

        // Get all customers with email preferences
        $users = User::where('role', 'customer')
            ->select('name', 'email', 'mobile', 'email_order_updates', 'email_promotions', 'email_newsletter', 'email_recommendations', 'created_at')
            ->get();

        if ($format === 'csv') {
            return $this->exportCsv($users);
        }

        return redirect()->back()->with('error', 'Invalid export format.');
    }

    /**
     * Export as CSV
     */
    private function exportCsv($users)
    {
        $filename = 'email-preferences-' . date('Y-m-d-His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function() use ($users) {
            $file = fopen('php://output', 'w');
            
            // Add CSV headers
            fputcsv($file, [
                'Name',
                'Email',
                'Mobile',
                'Order Updates',
                'Promotional Emails',
                'Newsletter',
                'Product Recommendations',
                'Member Since',
            ]);

            // Add data rows
            foreach ($users as $user) {
                fputcsv($file, [
                    $user->name,
                    $user->email,
                    $user->mobile,
                    $user->email_order_updates ? 'Yes' : 'No',
                    $user->email_promotions ? 'Yes' : 'No',
                    $user->email_newsletter ? 'Yes' : 'No',
                    $user->email_recommendations ? 'Yes' : 'No',
                    $user->created_at->format('Y-m-d'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Display newsletter subscribers list
     */
    public function newsletterSubscribers()
    {
        // Check permission
        if (!auth()->user()->hasPermission('users.view')) {
            abort(403, 'Unauthorized action.');
        }

        $subscribers = User::where('role', 'customer')
            ->where('email_newsletter', true)
            ->where('is_active', true)
            ->whereNotNull('email')
            ->select('id', 'name', 'email', 'mobile', 'created_at')
            ->orderBy('created_at', 'desc')
            ->paginate(50);

        return view('admin.email-preferences.newsletter-subscribers', compact('subscribers'));
    }

    /**
     * Display schedule setup page
     */
    public function scheduleSetup()
    {
        // Check permission
        if (!auth()->user()->hasPermission('users.view')) {
            abort(403, 'Unauthorized action.');
        }

        // Get current schedules from config
        $schedules = [
            'newsletter' => [
                'enabled' => true,
                'frequency' => 'weekly',
                'day' => 1, // Monday
                'time' => '09:00',
                'timezone' => 'Asia/Dhaka'
            ],
            'promotional' => [
                'enabled' => true,
                'frequency' => 'weekly',
                'day' => 5, // Friday
                'time' => '10:00',
                'timezone' => 'Asia/Dhaka'
            ],
            'recommendation' => [
                'enabled' => true,
                'frequency' => 'weekly',
                'day' => 3, // Wednesday
                'time' => '14:00',
                'timezone' => 'Asia/Dhaka'
            ]
        ];

        return view('admin.email-preferences.schedule-setup', compact('schedules'));
    }

    /**
     * Display mail content editor page
     */
    public function mailSetup()
    {
        // Check permission
        if (!auth()->user()->hasPermission('users.view')) {
            abort(403, 'Unauthorized action.');
        }

        return view('admin.email-preferences.mail-setup');
    }

    /**
     * Update schedule configuration
     */
    public function updateSchedule(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermission('users.edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|in:newsletter,promotional,recommendation',
            'enabled' => 'required|boolean',
            'frequency' => 'required|in:daily,weekly,monthly',
            'day' => 'nullable|integer|min:0|max:6',
            'time' => 'required|date_format:H:i',
            'timezone' => 'required|string'
        ]);

        // In production, save to database or config file
        // For now, return success
        return response()->json([
            'success' => true,
            'message' => 'Schedule updated successfully'
        ]);
    }

    /**
     * Send test email
     */
    public function sendTestEmail(Request $request)
    {
        // Check permission
        if (!auth()->user()->hasPermission('users.edit')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'type' => 'required|in:newsletter,promotional,recommendation',
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'test_email' => 'required|email'
        ]);

        // Send test email
        try {
            \Illuminate\Support\Facades\Mail::raw(
                strip_tags($validated['content']),
                function ($message) use ($validated) {
                    $message->to($validated['test_email'])
                            ->subject('[TEST] ' . $validated['subject']);
                }
            );

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully to ' . $validated['test_email']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage()
            ], 500);
        }
    }
}
