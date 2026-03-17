<?php

namespace Database\Seeders;

use App\Models\Reservation;
use App\Enums\ReservationStatus;
use Illuminate\Database\Seeder;

class ReservationSeeder extends Seeder
{
    public function run(): void
    {
        // Today's reservations
        Reservation::factory()->today()->confirmed()->count(4)->create();
        Reservation::factory()->today()->create(['status' => ReservationStatus::Pending]);

        // Upcoming reservations
        Reservation::factory()->count(15)->create();

        // Past reservations
        Reservation::factory()->count(10)->create([
            'reservation_date' => now()->subDays(rand(1, 14))->format('Y-m-d'),
            'status' => ReservationStatus::Completed,
        ]);
    }
}
