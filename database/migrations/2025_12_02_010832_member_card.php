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
        Schema::create('member_card', function (Blueprint $table) {
            $table->id('member_card_id');
            $table->foreignId('user_id')->constrained()->onDelete('Cascade');
            $table->foreignId('card_id')->constrained()->onDelete('Cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('member_card');
    }
};
