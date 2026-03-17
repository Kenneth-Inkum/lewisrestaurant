<?php

namespace Database\Seeders;

use App\Models\RestaurantTable;
use App\Enums\TableStatus;
use Illuminate\Database\Seeder;

class RestaurantTableSeeder extends Seeder
{
    public function run(): void
    {
        $tables = [
            // Main dining room
            ['number' => 1, 'capacity' => 2, 'section' => 'main', 'status' => TableStatus::Available],
            ['number' => 2, 'capacity' => 2, 'section' => 'main', 'status' => TableStatus::Available],
            ['number' => 3, 'capacity' => 4, 'section' => 'main', 'status' => TableStatus::Occupied],
            ['number' => 4, 'capacity' => 4, 'section' => 'main', 'status' => TableStatus::Available],
            ['number' => 5, 'capacity' => 4, 'section' => 'main', 'status' => TableStatus::Reserved],
            ['number' => 6, 'capacity' => 6, 'section' => 'main', 'status' => TableStatus::Available],
            ['number' => 7, 'capacity' => 6, 'section' => 'main', 'status' => TableStatus::Occupied],
            ['number' => 8, 'capacity' => 8, 'section' => 'main', 'status' => TableStatus::Available],
            // Patio
            ['number' => 9, 'capacity' => 2, 'section' => 'patio', 'status' => TableStatus::Available],
            ['number' => 10, 'capacity' => 2, 'section' => 'patio', 'status' => TableStatus::Available],
            ['number' => 11, 'capacity' => 4, 'section' => 'patio', 'status' => TableStatus::Occupied],
            ['number' => 12, 'capacity' => 4, 'section' => 'patio', 'status' => TableStatus::Available],
            // Bar seating
            ['number' => 13, 'name' => 'Bar Seat 1', 'capacity' => 1, 'section' => 'bar', 'status' => TableStatus::Available],
            ['number' => 14, 'name' => 'Bar Seat 2', 'capacity' => 1, 'section' => 'bar', 'status' => TableStatus::Occupied],
            ['number' => 15, 'name' => 'Bar Seat 3', 'capacity' => 1, 'section' => 'bar', 'status' => TableStatus::Available],
            // Private dining
            ['number' => 16, 'name' => 'Private Room', 'capacity' => 12, 'section' => 'private', 'status' => TableStatus::Available],
        ];

        foreach ($tables as $table) {
            RestaurantTable::create($table);
        }
    }
}
