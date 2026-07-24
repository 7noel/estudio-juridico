<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('case_activities', function (Blueprint $table) {
            $table->id();

            $table->foreignId('case_id')->constrained('cases')->cascadeOnDelete();

            // Tipo principal
            $table->string('type');

            // Subtipo (usa tus config)
            $table->string('subtype')->nullable();

            $table->text('title')->nullable();
            $table->text('description')->nullable();

            // Fecha de la actividad (audiencia, llamada, etc.)
            $table->timestamp('activity_at')->nullable();

            $table->foreignId('user_id')->constrained('users');

            $table->timestamps();
            $table->softDeletes();

            $table->index(['case_id','type','activity_at']);
            $table->index(['case_id', 'type', 'subtype']);
            $table->index('activity_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_activities');
    }
};
