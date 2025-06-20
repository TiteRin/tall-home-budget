<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\DistributionMethod;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bills', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('amount');
            $table->enum(
                'distribution_method', 
                array_column(
                    DistributionMethod::cases(), 
                    'value')
            )->default(DistributionMethod::EQUAL->value);
            $table->foreignId('household_id')->constrained('households');
            $table->foreignId('household_member_id')->constrained('household_members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bills');
    }
};
