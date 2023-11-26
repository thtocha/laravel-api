<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\DeskList;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class CardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $desk_lists = DeskList::all();


        $desk_lists->each(function ($desk_list) {
            Card::factory()
                ->count(3)
                ->create(['desk_list_id' => $desk_list->id]);
        });
    }
}
