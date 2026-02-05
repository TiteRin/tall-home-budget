<?php

use App\Enums\DistributionMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->date('spent_on');
            $table->unsignedInteger('amount');
            $table->foreignId('expense_tab_id')->constrained('expense_tabs');
            $table->foreignId('member_id')->constrained('members');
            $table->enum(
                'distribution_method',
                array_column(
                    DistributionMethod::cases(),
                    'value')
            )->default(DistributionMethod::EQUAL->value);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
