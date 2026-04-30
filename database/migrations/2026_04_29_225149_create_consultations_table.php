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
            $table->foreignId('lawyer_id')->nullable()->constrained('employees');
            $table->string('title');
            $table->text('description')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->string('status');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('evaluated_at')->nullable();
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
