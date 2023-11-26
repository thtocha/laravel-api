<?php

namespace Database\Seeders;

use App\Models\Desk;
use App\Models\DeskList;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;



class DeskListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $desks = Desk::all();

        // Создаем списки для каждой доски
        $desks->each(function ($desk) {
            DeskList::factory()
                ->count(3)
                ->create(['desk_id' => $desk->id]);
        });
    }
}
