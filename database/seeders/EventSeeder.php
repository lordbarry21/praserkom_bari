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
                'title' => 'Tyler, The Creator: CALL ME IF YOU GET LOST Tour',
                'category' => 'Musik',
                'ticket_price' => 1750000,
                'description' => 'Energetic and creative hip-hop performance by Tyler, The Creator featuring vibrant visuals and iconic hits.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Rex Orange County Live in Jakarta',
                'category' => 'Musik',
                'ticket_price' => 1200000,
                'description' => 'A soulful and heartwarming indie pop concert with sweet melodies and sing-alongs from Rex Orange County.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Juicy Luicy Live in Festival 2026',
                'category' => 'Festival',
                'ticket_price' => 375000,
                'description' => 'Penampilan festival musik spektakuler bersama Juicy Luicy, membawakan lagu-lagu hits seperti Lantas, Tanpa Tergesa, Lampu Kuning, dan Asing.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Seminar Sukses Finansial & Investasi Masa Depan bersama Timothy Ronald',
                'category' => 'Seminar',
                'ticket_price' => 500000,
                'description' => 'Seminar inspiratif eksklusif bersama Timothy Ronald membahas pola pikir finansial, strategi membangun aset, serta peluang bisnis kreatif untuk generasi muda.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'title' => 'Seminar Digital Entrepreneurship & Web3 bersama Gabriel Rey',
                'category' => 'Seminar',
                'ticket_price' => 450000,
                'description' => 'Seminar mendalam bersama Gabriel Rey mengupas tuntas inovasi teknologi digital, fintech, blockchain, dan rahasia membangun startup sukses dari nol.',
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
