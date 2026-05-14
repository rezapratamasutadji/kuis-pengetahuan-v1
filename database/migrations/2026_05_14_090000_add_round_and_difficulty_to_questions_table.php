<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->dropUnique(['category_id', 'number']);
            $table->string('round', 20)->default('qualification')->after('category_id');
            $table->string('difficulty', 20)->default('easy')->after('number');
            $table->unique(['category_id', 'round', 'number']);
        });
    }

    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table): void {
            $table->dropUnique(['category_id', 'round', 'number']);
            $table->dropColumn(['round', 'difficulty']);
            $table->unique(['category_id', 'number']);
        });
    }
};
