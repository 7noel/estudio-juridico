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
        // Índices para consultations (reportes financieros y operativos)
        Schema::table('consultations', function (Blueprint $table) {
            $table->index(['establishment_id', 'created_at']);
            $table->index(['legal_specialty_id', 'created_at']);
            $table->index(['lawyer_id', 'created_at']);
            $table->index(['status', 'created_at']);
            $table->index(['service_type', 'created_at']);
        });

        // Índices para consultation_installments (cobranza)
        Schema::table('consultation_installments', function (Blueprint $table) {
            $table->index(['consultation_id', 'due_date']);
            $table->index(['establishment_id', 'due_date']);
        });

        // Índices para payments (reportes de caja y financieros)
        Schema::table('payments', function (Blueprint $table) {
            $table->index(['payment_date', 'consultation_id']);
            $table->index(['establishment_id', 'payment_date']);
            $table->index(['payment_method', 'payment_date']);
        });

        // Índices para expenses (reportes de gastos)
        Schema::table('expenses', function (Blueprint $table) {
            $table->index(['expense_date', 'case_id']);
            $table->index(['establishment_id', 'expense_date']);
            $table->index(['category', 'expense_date']);
            $table->index(['payment_method', 'expense_date']);
        });

        // Índices para cases (reportes operativos y de abogados)
        Schema::table('cases', function (Blueprint $table) {
            $table->index(['establishment_id', 'opened_at']);
            $table->index(['lawyer_id', 'opened_at']);
            $table->index(['legal_specialty_id', 'opened_at']);
            $table->index(['client_id', 'opened_at']);
            $table->index(['service_type', 'opened_at']);
        });

        // Índices para clients (búsqueda y reportes)
        Schema::table('clients', function (Blueprint $table) {
            $table->index('full_name');
            $table->index('document_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('consultations', function (Blueprint $table) {
            $table->dropIndex(['establishment_id', 'created_at']);
            $table->dropIndex(['legal_specialty_id', 'created_at']);
            $table->dropIndex(['lawyer_id', 'created_at']);
            $table->dropIndex(['status', 'created_at']);
            $table->dropIndex(['service_type', 'created_at']);
        });

        Schema::table('consultation_installments', function (Blueprint $table) {
            $table->dropIndex(['consultation_id', 'due_date']);
            $table->dropIndex(['establishment_id', 'due_date']);
        });

        Schema::table('payments', function (Blueprint $table) {
            $table->dropIndex(['payment_date', 'consultation_id']);
            $table->dropIndex(['establishment_id', 'payment_date']);
            $table->dropIndex(['payment_method', 'payment_date']);
        });

        Schema::table('expenses', function (Blueprint $table) {
            $table->dropIndex(['expense_date', 'case_id']);
            $table->dropIndex(['establishment_id', 'expense_date']);
            $table->dropIndex(['category', 'expense_date']);
            $table->dropIndex(['payment_method', 'expense_date']);
        });

        Schema::table('cases', function (Blueprint $table) {
            $table->dropIndex(['establishment_id', 'opened_at']);
            $table->dropIndex(['lawyer_id', 'opened_at']);
            $table->dropIndex(['legal_specialty_id', 'opened_at']);
            $table->dropIndex(['client_id', 'opened_at']);
            $table->dropIndex(['service_type', 'opened_at']);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropIndex('full_name');
            $table->dropIndex('document_number');
        });
    }
};
