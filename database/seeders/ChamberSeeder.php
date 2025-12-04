<?php

namespace Database\Seeders;

use App\Models\Chamber;
use Illuminate\Database\Seeder;

/**
 * Chamber Seeder
 * 
 * Seeds sample chambers/branches for appointment system
 * 
 * @category Seeders
 * @package  Database\Seeders
 * @created  2025-11-26
 */
class ChamberSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $chambers = [
            [
                'name' => 'ঢাকা চেম্বার',
                'slug' => 'dhaka-chamber',
                'address' => 'মিরপুর, ঢাকা - ১২১৬',
                'phone' => '01700000001',
                'email' => 'dhaka@example.com',
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
                'slot_duration' => 30, // 30 মিনিট per appointment
                'break_start' => 780, // 13:00 = 780 minutes from midnight (13 * 60)
                'break_duration' => 60, // 1 hour break
                'is_active' => true,
                'display_order' => 1,
            ],
            [
                'name' => 'চট্টগ্রাম চেম্বার',
                'slug' => 'chittagong-chamber',
                'address' => 'আগ্রাবাদ, চট্টগ্রাম - ৪১০০',
                'phone' => '01700000002',
                'email' => 'chittagong@example.com',
                'operating_hours' => [
                    'saturday' => ['open' => '10:00', 'close' => '18:00', 'is_open' => true],
                    'sunday' => ['open' => '10:00', 'close' => '18:00', 'is_open' => true],
                    'monday' => ['open' => '10:00', 'close' => '18:00', 'is_open' => true],
                    'tuesday' => ['open' => '10:00', 'close' => '18:00', 'is_open' => true],
                    'wednesday' => ['open' => '10:00', 'close' => '18:00', 'is_open' => true],
                    'thursday' => ['open' => '10:00', 'close' => '18:00', 'is_open' => true],
                    'friday' => ['is_open' => false], // শুক্রবার বন্ধ
                ],
                'closed_days' => ['friday'],
                'slot_duration' => 30,
                'break_start' => 840, // 14:00 = 840 minutes from midnight (14 * 60)
                'break_duration' => 60, // 1 hour break
                'is_active' => true,
                'display_order' => 2,
            ],
            [
                'name' => 'সিলেট চেম্বার',
                'slug' => 'sylhet-chamber',
                'address' => 'জিন্দাবাজার, সিলেট - ৩১০০',
                'phone' => '01700000003',
                'email' => 'sylhet@example.com',
                'operating_hours' => [
                    'saturday' => ['open' => '09:30', 'close' => '16:30', 'is_open' => true],
                    'sunday' => ['open' => '09:30', 'close' => '16:30', 'is_open' => true],
                    'monday' => ['open' => '09:30', 'close' => '16:30', 'is_open' => true],
                    'tuesday' => ['open' => '09:30', 'close' => '16:30', 'is_open' => true],
                    'wednesday' => ['open' => '09:30', 'close' => '16:30', 'is_open' => true],
                    'thursday' => ['open' => '09:30', 'close' => '16:30', 'is_open' => true],
                    'friday' => ['is_open' => false],
                ],
                'closed_days' => ['friday'],
                'slot_duration' => 30,
                'break_start' => 780, // 13:00 = 780 minutes
                'break_duration' => 30, // 30 minutes break
                'is_active' => true,
                'display_order' => 3,
            ],
        ];

        foreach ($chambers as $chamberData) {
            Chamber::updateOrCreate(
                ['slug' => $chamberData['slug']],
                $chamberData
            );
        }

        $this->command->info('Chamber seeder completed successfully!');
        $this->command->info('Created ' . count($chambers) . ' chambers.');
    }
}
