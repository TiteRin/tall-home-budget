<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expense_tabs', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedTinyInteger('period_start_day');
            $table->unsignedTinyInteger('period_end_day');
            $table->foreignId('default_member_id')->nullable()->constrained('members');
            $table->string('default_distribution_method')->nullable();
            $table->timestamps();
            $table->foreignId('household_id')->constrained('households')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expense_tabs');
    }
};
