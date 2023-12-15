<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('desk_lists', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('name');
            $table->unsignedBigInteger('desk_id');

            $table->foreign('desk_id')->references('id')->on('desks')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('desk_lists');
    }
};
