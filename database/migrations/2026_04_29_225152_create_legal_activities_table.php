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
        Schema::create('legal_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('case_id')->constrained('cases');
            $table->string('activity_type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->date('activity_date');
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
        Schema::dropIfExists('legal_activities');
    }
};
