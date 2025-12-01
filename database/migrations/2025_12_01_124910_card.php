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
        Schema::create('card', function(Blueprint $table) {
            $table->id('card_id');
            $table->string('card_title');
            $table->string('image')->nullable();
            $table->string('description')->nullable();
            $table->date('dates')->nullable();
            $table->float('position');
            $table->foreignId('list_id')->constrained()->onDelete('Cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card');
    }
};
