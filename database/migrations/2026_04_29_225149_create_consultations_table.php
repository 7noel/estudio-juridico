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
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->string('service_type');
            $table->foreignId('legal_specialty_id')->constrained();
            $table->foreignId('legal_subject_id')->constrained();
            $table->foreignId('lawyer_id')->nullable()->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('status');
            $table->date('last_follow_up_at')->nullable();
            $table->string('last_follow_up_result')->nullable();
            $table->date('next_follow_up_at')->nullable();
            $table->foreignId('user_id')->constrained('users');
            $table->timestamp('prospect_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
