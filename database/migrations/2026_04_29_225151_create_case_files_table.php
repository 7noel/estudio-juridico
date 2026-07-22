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
        Schema::create('cases', function (Blueprint $table) {
            $table->id();
            $table->foreignId('establishment_id')->constrained();
            $table->foreignId('consultation_id')->constrained();
            $table->foreignId('client_id')->constrained();
            $table->foreignId('lawyer_id')->constrained('users');
            $table->string('slug')->unique();
            $table->string('court_name')->nullable();
            $table->string('case_number')->nullable();
            $table->string('service_type');
            $table->foreignId('legal_specialty_id')->constrained();
            $table->foreignId('legal_subject_id')->constrained();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('status');
            $table->string('result')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('closed_at')->nullable();

            $table->index(['status', 'opened_at']);
            $table->foreignId('created_by')->constrained('users');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_files');
    }
};
