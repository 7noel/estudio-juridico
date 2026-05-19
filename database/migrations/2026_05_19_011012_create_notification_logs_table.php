<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {

            $table->id();

            $table->string('type');

            $table->unsignedBigInteger('related_id');

            $table->string('phone')->nullable();

            $table->timestamp('sent_at');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};