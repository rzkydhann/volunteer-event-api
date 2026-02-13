<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $alice = User::create(['name' => 'Alice', 'email' => 'alice@example.com', 'password' => Hash::make('password123')]);
        $bob   = User::create(['name' => 'Bob',   'email' => 'bob@example.com',   'password' => Hash::make('password123')]);
        $charlie = User::create(['name' => 'Charlie', 'email' => 'charlie@example.com', 'password' => Hash::make('password123')]);

        $event1 = Event::create(['title' => 'Bersih Pantai Ancol',   'description' => 'Bersih-bersih pantai bersama.',         'event_date' => now()->addDays(7),  'user_id' => $alice->id]);
        $event2 = Event::create(['title' => 'Donor Darah PMI',       'description' => 'Kegiatan donor darah rutin.',           'event_date' => now()->addDays(14), 'user_id' => $alice->id]);
        $event3 = Event::create(['title' => 'Mengajar Panti Asuhan', 'description' => 'Relawan pengajar anak panti asuhan.',   'event_date' => now()->addDays(3),  'user_id' => $bob->id]);

        $event1->participants()->attach([$bob->id, $charlie->id]);
        $event2->participants()->attach([$charlie->id]);
        $event3->participants()->attach([$alice->id, $charlie->id]);

        $this->command->info('✅ Seeder selesai! Login: alice@example.com / password123');
    }
}