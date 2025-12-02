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
        Schema::create('label_template', function(Blueprint $table) {
            $table->id('label_template_id');
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
        Schema::dropIfExists('label_template');
    }
};
