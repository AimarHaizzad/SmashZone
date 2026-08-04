<?php

namespace Database\Seeders;

use App\Models\Court;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use RuntimeException;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $ownerEmail = env('SEED_OWNER_EMAIL', 'admin@smashzone.my');
        $ownerPassword = $this->requiredSeedPassword('SEED_OWNER_PASSWORD');
        $staffPassword = env('SEED_STAFF_PASSWORD') ?: $ownerPassword;

        $owner = User::firstOrCreate(
            ['email' => $ownerEmail],
            [
                'name' => 'Ahmad Razif',
                'password' => bcrypt($ownerPassword),
                'role' => 'owner',
                'phone' => '+60 12-345 6789',
                'position' => 'Facility Director',
                'email_verified_at' => now(),
                'remember_token' => Str::random(10),
            ]
        );

        $staffMembers = [
            ['name' => 'Siti Nurhaliza', 'email' => 'siti@smashzone.my', 'position' => 'Front Desk Supervisor'],
            ['name' => 'Raj Kumar', 'email' => 'raj@smashzone.my', 'position' => 'Court Operations'],
        ];

        foreach ($staffMembers as $staffData) {
            User::firstOrCreate(
                ['email' => $staffData['email']],
                [
                    'name' => $staffData['name'],
                    'password' => bcrypt($staffPassword),
                    'role' => 'staff',
                    'position' => $staffData['position'],
                    'phone' => '+60 11-000 0000',
                    'email_verified_at' => now(),
                ]
            );
        }

        $courts = [
            [
                'name' => 'Premier Court A',
                'description' => 'International-grade wooden flooring with professional lighting. Ideal for competitive play and tournaments.',
                'location' => 'center',
                'status' => 'active',
            ],
            [
                'name' => 'Premier Court B',
                'description' => 'Premium synthetic surface with climate control. Preferred for training sessions and league matches.',
                'location' => 'center',
                'status' => 'active',
            ],
            [
                'name' => 'Standard Court C',
                'description' => 'Well-maintained court suitable for recreational play, coaching clinics, and corporate bookings.',
                'location' => 'middle',
                'status' => 'active',
            ],
            [
                'name' => 'Standard Court D',
                'description' => 'Accessible court with equipment storage nearby. Perfect for beginners and casual doubles.',
                'location' => 'middle',
                'status' => 'active',
            ],
        ];

        foreach ($courts as $courtData) {
            Court::firstOrCreate(
                ['name' => $courtData['name'], 'owner_id' => $owner->id],
                $courtData
            );
        }

        $products = [
            ['name' => 'Yonex Power Cushion 65 Z3', 'description' => 'Professional badminton shoes with power cushion technology.', 'price' => 449.00, 'quantity' => 12, 'category' => 'shoes', 'brand' => 'Yonex'],
            ['name' => 'Li-Ning Aeronaut 9000', 'description' => 'High-speed racket engineered for offensive players.', 'price' => 899.00, 'quantity' => 8, 'category' => 'rackets', 'brand' => 'Li-Ning'],
            ['name' => 'Victor Master No.1', 'description' => 'Tournament shuttlecocks, tube of 12.', 'price' => 89.00, 'quantity' => 40, 'category' => 'shuttlecocks', 'brand' => 'Victor'],
            ['name' => 'Yonex Pro Racket Bag 9', 'description' => 'Thermo-guard racket bag with shoe compartment.', 'price' => 329.00, 'quantity' => 15, 'category' => 'bags', 'brand' => 'Yonex'],
            ['name' => 'Apacs Dri-Fit Jersey', 'description' => 'Breathable competition jersey, unisex fit.', 'price' => 79.00, 'quantity' => 30, 'category' => 'clothing', 'brand' => 'Apacs'],
            ['name' => 'Yonex Super Grap', 'description' => 'Overgrip tape, pack of 3.', 'price' => 18.00, 'quantity' => 100, 'category' => 'accessories', 'brand' => 'Yonex'],
        ];

        foreach ($products as $productData) {
            Product::firstOrCreate(['name' => $productData['name']], $productData);
        }

        $this->command->info('SmashZone facility data seeded successfully.');
        $this->command->info("Owner account: {$ownerEmail}");
        $this->command->info('Staff accounts: siti@smashzone.my, raj@smashzone.my');
        $this->command->warn('Passwords were taken from SEED_OWNER_PASSWORD / SEED_STAFF_PASSWORD in .env — they are never stored in source code.');
    }

    private function requiredSeedPassword(string $key): string
    {
        $password = env($key);

        if (empty($password)) {
            throw new RuntimeException("Missing {$key}. Set it in your .env file before running db:seed.");
        }

        return $password;
    }
}
