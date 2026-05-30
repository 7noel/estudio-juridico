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
        Schema::create('consultation_installments', function (Blueprint $table) {

            $table->id();
            $table->foreignId('establishment_id')->constrained();
            $table->foreignId('consultation_id')->constrained();
            $table->integer('installment_number');
            $table->decimal('amount', 10, 2);
            $table->decimal('paid_amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consultation_installments');
    }
};
