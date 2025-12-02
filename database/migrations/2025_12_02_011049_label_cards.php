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
        Schema::create('label_cards', function(Blueprint $table) {
            $table->id('label_card_id');
            $table->foreignId('label_id')->constrained()->onDelete('Cascade');
            $table->foreignId('card_id')->constrained()->onDelete('Cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
