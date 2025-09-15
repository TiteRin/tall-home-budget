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
        Schema::rename('household_members', 'members');

        Schema::table('bills', function (Blueprint $table) {
            $table->renameColumn('household_member_id', 'member_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bills', function (Blueprint $table) {
            $table->renameColumn('member_id', 'household_member_id');
        });

        Schema::rename('members', 'household_members');
    }
};
