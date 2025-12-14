<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (!Schema::hasColumn('comments', 'card_id')) {
                $table->foreignId('card_id')->constrained()->onDelete('cascade');
            }
            
            if (!Schema::hasColumn('comments', 'user_id')) {
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
            }
            
            if (Schema::hasColumn('comments', 'dates')) {
                $table->dropColumn('dates');
            }
        });
    }

    public function down(): void
    {
        Schema::table('comments', function (Blueprint $table) {
            if (Schema::hasColumn('comments', 'card_id')) {
                $table->dropForeign(['card_id']);
                $table->dropColumn('card_id');
            }
            
            if (Schema::hasColumn('comments', 'user_id')) {
                $table->dropForeign(['user_id']);
                $table->dropColumn('user_id');
            }
            
            $table->date('dates');
        });
    }
};