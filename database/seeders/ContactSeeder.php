<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ContactSetting;
use App\Models\ContactFaq;

class ContactSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->seedContactSettings();
        $this->seedContactFaqs();
    }

    /**
     * Seed contact settings
     */
    private function seedContactSettings(): void
    {
        $settings = [
            // General Information
            [
                'key' => 'company_name',
                'group' => 'general',
                'value' => config('app.name'),
                'type' => 'text',
                'description' => 'Company name',
                'order' => 1,
            ],
            [
                'key' => 'email',
                'group' => 'general',
                'value' => 'info@example.com',
                'type' => 'email',
                'description' => 'Contact email address',
                'order' => 2,
            ],
            [
                'key' => 'phone',
                'group' => 'general',
                'value' => '+880 1234-567890',
                'type' => 'tel',
                'description' => 'Contact phone number',
                'order' => 3,
            ],
            [
                'key' => 'whatsapp',
                'group' => 'general',
                'value' => '+880 1234-567890',
                'type' => 'tel',
                'description' => 'WhatsApp number',
                'order' => 4,
            ],
            [
                'key' => 'address',
                'group' => 'general',
                'value' => '123 Main Street',
                'type' => 'text',
                'description' => 'Business address',
                'order' => 5,
            ],
            [
                'key' => 'city',
                'group' => 'general',
                'value' => 'Dhaka',
                'type' => 'text',
                'description' => 'City',
                'order' => 6,
            ],
            [
                'key' => 'state',
                'group' => 'general',
                'value' => 'Dhaka',
                'type' => 'text',
                'description' => 'State/Division',
                'order' => 7,
            ],
            [
                'key' => 'zip',
                'group' => 'general',
                'value' => '1000',
                'type' => 'text',
                'description' => 'ZIP/Postal code',
                'order' => 8,
            ],
            [
                'key' => 'country',
                'group' => 'general',
                'value' => 'Bangladesh',
                'type' => 'text',
                'description' => 'Country',
                'order' => 9,
            ],
            [
                'key' => 'business_hours',
                'group' => 'general',
                'value' => 'Sat - Thu: 9:00 AM - 6:00 PM',
                'type' => 'text',
                'description' => 'Business hours',
                'order' => 10,
            ],

            // Map Settings
            [
                'key' => 'map_latitude',
                'group' => 'map',
                'value' => '23.8103',
                'type' => 'text',
                'description' => 'Map latitude coordinate',
                'order' => 1,
            ],
            [
                'key' => 'map_longitude',
                'group' => 'map',
                'value' => '90.4125',
                'type' => 'text',
                'description' => 'Map longitude coordinate',
                'order' => 2,
            ],
            [
                'key' => 'map_zoom',
                'group' => 'map',
                'value' => '15',
                'type' => 'text',
                'description' => 'Map zoom level',
                'order' => 3,
            ],
            [
                'key' => 'map_embed_code',
                'group' => 'map',
                'value' => '',
                'type' => 'textarea',
                'description' => 'Google Maps HTML embed code (iframe). If provided, this will be used instead of API-based map.',
                'order' => 4,
            ],

            // Social Media
            [
                'key' => 'facebook',
                'group' => 'social',
                'value' => '',
                'type' => 'url',
                'description' => 'Facebook page URL',
                'order' => 1,
            ],
            [
                'key' => 'twitter',
                'group' => 'social',
                'value' => '',
                'type' => 'url',
                'description' => 'Twitter profile URL',
                'order' => 2,
            ],
            [
                'key' => 'instagram',
                'group' => 'social',
                'value' => '',
                'type' => 'url',
                'description' => 'Instagram profile URL',
                'order' => 3,
            ],
            [
                'key' => 'linkedin',
                'group' => 'social',
                'value' => '',
                'type' => 'url',
                'description' => 'LinkedIn profile URL',
                'order' => 4,
            ],
            [
                'key' => 'youtube',
                'group' => 'social',
                'value' => '',
                'type' => 'url',
                'description' => 'YouTube channel URL',
                'order' => 5,
            ],

            // Chamber Information
            [
                'key' => 'chamber_title',
                'group' => 'chamber',
                'value' => 'Corporate Office',
                'type' => 'text',
                'description' => 'Chamber/Office title',
                'order' => 1,
            ],
            [
                'key' => 'chamber_address',
                'group' => 'chamber',
                'value' => 'Suite 456, Business Tower, Gulshan Avenue',
                'type' => 'textarea',
                'description' => 'Chamber address',
                'order' => 2,
            ],
            [
                'key' => 'chamber_phone',
                'group' => 'chamber',
                'value' => '+880 2-9876543',
                'type' => 'tel',
                'description' => 'Chamber phone number',
                'order' => 3,
            ],
            [
                'key' => 'chamber_email',
                'group' => 'chamber',
                'value' => 'corporate@example.com',
                'type' => 'email',
                'description' => 'Chamber email address',
                'order' => 4,
            ],
            [
                'key' => 'chamber_hours',
                'group' => 'chamber',
                'value' => 'Mon - Fri: 10:00 AM - 5:00 PM',
                'type' => 'text',
                'description' => 'Chamber business hours',
                'order' => 5,
            ],

            // SEO Settings
            [
                'key' => 'seo_title',
                'group' => 'seo',
                'value' => 'Contact Us - Get in Touch',
                'type' => 'text',
                'description' => 'SEO page title',
                'order' => 1,
            ],
            [
                'key' => 'seo_description',
                'group' => 'seo',
                'value' => 'Contact us for any inquiries, support, or feedback. We are here to help you with all your questions and concerns.',
                'type' => 'textarea',
                'description' => 'SEO meta description (150-160 characters recommended)',
                'order' => 2,
            ],
            [
                'key' => 'seo_keywords',
                'group' => 'seo',
                'value' => 'contact, support, customer service, help, inquiry, get in touch',
                'type' => 'textarea',
                'description' => 'SEO keywords (comma separated)',
                'order' => 3,
            ],
            [
                'key' => 'seo_image',
                'group' => 'seo',
                'value' => '',
                'type' => 'image',
                'description' => 'SEO image for social media (Open Graph & Twitter Card)',
                'order' => 4,
            ],
            [
                'key' => 'seo_og_title',
                'group' => 'seo',
                'value' => 'Contact Us - Get in Touch',
                'type' => 'text',
                'description' => 'Open Graph title (for Facebook, LinkedIn)',
                'order' => 5,
            ],
            [
                'key' => 'seo_og_description',
                'group' => 'seo',
                'value' => 'Contact us for any inquiries, support, or feedback. We are here to help you.',
                'type' => 'textarea',
                'description' => 'Open Graph description',
                'order' => 6,
            ],
            [
                'key' => 'seo_twitter_title',
                'group' => 'seo',
                'value' => 'Contact Us - Get in Touch',
                'type' => 'text',
                'description' => 'Twitter card title',
                'order' => 7,
            ],
            [
                'key' => 'seo_twitter_description',
                'group' => 'seo',
                'value' => 'Contact us for any inquiries, support, or feedback.',
                'type' => 'textarea',
                'description' => 'Twitter card description',
                'order' => 8,
            ],
        ];

        foreach ($settings as $setting) {
            $this->upsertSetting($setting);
        }
    }

    /**
     * Seed contact FAQs
     */
    private function seedContactFaqs(): void
    {
        $faqs = [
            [
                'question' => 'What are your business hours?',
                'answer' => 'We are open Saturday to Thursday from 9:00 AM to 6:00 PM. We are closed on Fridays and public holidays.',
                'order' => 1,
            ],
            [
                'question' => 'How can I contact customer support?',
                'answer' => 'You can contact our customer support team via email, phone, or WhatsApp during business hours. You can also fill out the contact form above and we will get back to you within 24 hours.',
                'order' => 2,
            ],
            [
                'question' => 'Do you offer international shipping?',
                'answer' => 'Yes, we offer international shipping to most countries. Shipping costs and delivery times vary depending on your location. Please contact us for specific details about shipping to your country.',
                'order' => 3,
            ],
            [
                'question' => 'What is your return policy?',
                'answer' => 'We offer a 30-day return policy for most products. Items must be unused and in their original packaging. Please contact our customer support team to initiate a return.',
                'order' => 4,
            ],
            [
                'question' => 'How long does it take to get a response?',
                'answer' => 'We typically respond to all inquiries within 24 hours during business days. For urgent matters, please call us directly or contact us via WhatsApp.',
                'order' => 5,
            ],
            [
                'question' => 'Can I visit your physical store?',
                'answer' => 'Yes, you are welcome to visit our showroom during business hours. We recommend calling ahead to ensure availability of specific products you wish to see.',
                'order' => 6,
            ],
            [
                'question' => 'Do you offer bulk or wholesale pricing?',
                'answer' => 'Yes, we offer special pricing for bulk orders and wholesale customers. Please contact our sales team with your requirements for a custom quote.',
                'order' => 7,
            ],
            [
                'question' => 'How do I track my order?',
                'answer' => 'Once your order is shipped, you will receive a tracking number via email and SMS. You can use this number to track your shipment on our website or the courier\'s website.',
                'order' => 8,
            ],
        ];

        foreach ($faqs as $faq) {
            $this->upsertFaq($faq);
        }
    }

    /**
     * Upsert a single contact setting
     */
    private function upsertSetting(array $data): void
    {
        $existing = ContactSetting::where('key', $data['key'])->first();

        if (!$existing) {
            ContactSetting::create($data);
            $this->command->info("✓ Created contact setting: {$data['key']}");
        } else {
            $hasChanges = false;
            $changes = [];

            foreach ($data as $field => $value) {
                if ($field !== 'key' && $existing->$field != $value) {
                    $hasChanges = true;
                    $changes[] = $field;
                }
            }

            if ($hasChanges) {
                $existing->update($data);
                $this->command->info("✓ Updated contact setting: {$data['key']} (" . implode(', ', $changes) . ")");
            }
        }
    }

    /**
     * Upsert a single FAQ
     */
    private function upsertFaq(array $data): void
    {
        $existing = ContactFaq::where('question', $data['question'])->first();

        if (!$existing) {
            ContactFaq::create($data);
            $this->command->info("✓ Created FAQ: {$data['question']}");
        } else {
            $hasChanges = false;
            $changes = [];

            foreach ($data as $field => $value) {
                if ($field !== 'question' && $existing->$field != $value) {
                    $hasChanges = true;
                    $changes[] = $field;
                }
            }

            if ($hasChanges) {
                $existing->update($data);
                $this->command->info("✓ Updated FAQ: {$data['question']} (" . implode(', ', $changes) . ")");
            }
        }
    }
}
