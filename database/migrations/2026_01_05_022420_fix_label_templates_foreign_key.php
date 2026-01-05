<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('label_templates', function (Blueprint $table) {
            // Drop the old foreign key using the ORIGINAL name
            $table->dropForeign('label_templates_card_id_foreign'); // ← Original constraint name
            
            // Add the correct foreign key
            $table->foreign('card_template_id')
                  ->references('id')
                  ->on('card_templates')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('label_templates', function (Blueprint $table) {
            $table->dropForeign(['card_template_id']);
            $table->foreign('card_template_id')
                  ->references('id')
                  ->on('cards')
                  ->onDelete('cascade');
        });
    }
};