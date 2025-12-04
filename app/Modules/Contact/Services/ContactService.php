<?php

namespace App\Modules\Contact\Services;

use App\Models\ContactMessage;
use App\Models\ContactSetting;
use App\Models\ContactFaq;
use App\Mail\ContactMessageReceived;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    /**
     * Store a new contact message
     */
    public function storeMessage(array $data): ContactMessage
    {
        try {
            DB::beginTransaction();

            // Add IP and user agent
            $data['ip_address'] = request()->ip();
            $data['user_agent'] = request()->userAgent();

            $message = ContactMessage::create($data);

            DB::commit();

            // Log the new message
            Log::info('New contact message received', [
                'message_id' => $message->id,
                'email' => $message->email,
            ]);

            // Send email notification to admin
            try {
                $adminEmail = ContactSetting::get('email', config('mail.from.address'));
                if ($adminEmail) {
                    Mail::to($adminEmail)->send(new ContactMessageReceived($message));
                }
            } catch (\Exception $e) {
                Log::error('Error sending contact message email: ' . $e->getMessage());
                // Don't throw - message is saved, email is secondary
            }

            return $message;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing contact message: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all active FAQs
     */
    public function getActiveFaqs()
    {
        return ContactFaq::active()->ordered()->get();
    }

    /**
     * Get contact page settings
     */
    public function getContactSettings(): array
    {
        return [
            'company_name' => ContactSetting::get('company_name', config('app.name')),
            'email' => ContactSetting::get('email', ''),
            'phone' => ContactSetting::get('phone', ''),
            'whatsapp' => ContactSetting::get('whatsapp', ''),
            'address' => ContactSetting::get('address', ''),
            'city' => ContactSetting::get('city', ''),
            'state' => ContactSetting::get('state', ''),
            'zip' => ContactSetting::get('zip', ''),
            'country' => ContactSetting::get('country', ''),
            'map_latitude' => ContactSetting::get('map_latitude', '23.8103'),
            'map_longitude' => ContactSetting::get('map_longitude', '90.4125'),
            'map_zoom' => ContactSetting::get('map_zoom', '15'),
            'business_hours' => ContactSetting::get('business_hours', ''),
            'facebook' => ContactSetting::get('facebook', ''),
            'twitter' => ContactSetting::get('twitter', ''),
            'instagram' => ContactSetting::get('instagram', ''),
            'linkedin' => ContactSetting::get('linkedin', ''),
            'map_embed_code' => ContactSetting::get('map_embed_code', ''),
            'youtube' => ContactSetting::get('youtube', ''),
        ];
    }

    /**
     * Get chamber information
     */
    public function getChamberInfo(): array
    {
        return [
            'title' => ContactSetting::get('chamber_title', 'Corporate Office'),
            'address' => ContactSetting::get('chamber_address', ''),
            'phone' => ContactSetting::get('chamber_phone', ''),
            'email' => ContactSetting::get('chamber_email', ''),
            'hours' => ContactSetting::get('chamber_hours', ''),
        ];
    }

    /**
     * Update contact message status
     */
    public function updateMessageStatus(ContactMessage $message, string $status, ?string $note = null): ContactMessage
    {
        $message->update([
            'status' => $status,
            'admin_note' => $note,
            'read_at' => $status !== 'unread' ? now() : null,
        ]);

        return $message->fresh();
    }
}
