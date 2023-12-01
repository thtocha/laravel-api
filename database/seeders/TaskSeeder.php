<?php

namespace Database\Seeders;

use App\Models\Card;
use App\Models\Task;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cards = Card::all();


        $cards->each(function ($card) {
            Task::factory()
                ->count(2)
                ->create(['card_id' => $card->id]);
        });
    }
}
