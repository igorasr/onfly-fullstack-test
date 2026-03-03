<?php

namespace Database\Seeders;

use App\Models\TravelRequest;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TravelRequestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TravelRequest::factory()->count(20)->create();
    }
}
