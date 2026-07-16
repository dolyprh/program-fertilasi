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
        Schema::create('classifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('doctors')->restrictOnDelete();
            $table->foreignId('current_promil_id')->constrained('current_promils')->restrictOnDelete();
            $table->foreignId('current_action_id')->constrained('current_actions')->restrictOnDelete();
            $table->foreignId('poa1_option_id')->constrained('poa1_options')->restrictOnDelete();
            $table->string('case_code')->unique();
            $table->string('patient_name');
            $table->date('visit_date');
            $table->date('date_of_birth');
            $table->unsignedSmallInteger('age');
            $table->string('prediction_result')->nullable();
            $table->string('rule_code')->nullable();
            $table->json('decision_path')->nullable();
            $table->decimal('confidence', 5, 2)->nullable();
            $table->unsignedInteger('support_count')->nullable();
            $table->decimal('support_percentage', 5, 2)->nullable();
            $table->string('status')->default('classified');
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'status']);
            $table->index('current_promil_id');
            $table->index('prediction_result');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classifications');
    }
};
