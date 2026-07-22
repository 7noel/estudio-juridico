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
        Schema::create('consultation_follow_ups', function (Blueprint $table) {

            $table->id();

            $table->foreignId('consultation_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->constrained()
                ->restrictOnDelete();

            $table->date('contact_date');

            $table->string('communication_type', 20);

            $table->string('result', 30);

            $table->date('next_contact_date')->nullable();

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->softDeletes();

            // Índices
            $table->index('consultation_id');
            $table->index('user_id');
            $table->index('contact_date');
            $table->index('next_contact_date');
            $table->index('communication_type');
            $table->index('result');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_follow_ups');
    }
};
