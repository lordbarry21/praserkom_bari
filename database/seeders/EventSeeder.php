<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('events')->insert([
            [
                'name' => 'Tyler, The Creator: CALL ME IF YOU GET LOST Tour',
                'category' => 'Musik',
                'price' => 1750000,
                'description' => 'Energetic and creative hip-hop performance by Tyler, The Creator featuring vibrant visuals and iconic hits.',
                'image' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Rex Orange County Live in Concert',
                'category' => 'Musik',
                'price' => 1200000,
                'description' => 'A soulful and heartwarming indie pop concert with sweet melodies and sing-alongs from Rex Orange County.',
                'image' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => "Drake: It's All A Blur Tour",
                'category' => 'Musik',
                'price' => 2500000,
                'description' => 'A blockbuster arena concert by Drake with state-of-the-art stage production and chart-topping rap anthems.',
                'image' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Neck Deep Asia Tour',
                'category' => 'Musik',
                'price' => 850000,
                'description' => 'High-energy pop-punk concert featuring Neck Deep, packed with nostalgic riffs, moshpits, and loud anthems.',
                'image' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Daniel Caesar: Superpowers World Tour',
                'category' => 'Musik',
                'price' => 1500000,
                'description' => 'An intimate R&B live concert experience with smooth vocals, soulful acoustic sets, and hit ballads.',
                'image' => '',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
