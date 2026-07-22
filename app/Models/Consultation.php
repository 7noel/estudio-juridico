<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'establishment_id',
        'client_id',
        'service_type',
        'legal_specialty_id',
        'legal_subject_id',
        'lawyer_id',
        'title',
        'description',
        'total_amount',
        'status',
        'created_by',
        'assigned_at',
        'evaluated_at',
        'quoted_at',
        'accepted_at',
        'rejected_at',
    ];

    protected $casts = [

        'assigned_at' => 'datetime',
        'evaluated_at' => 'datetime',
        'quoted_at' => 'datetime',
        'accepted_at' => 'datetime',
        'rejected_at' => 'datetime',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function specialty()
    {
        return $this->belongsTo(LegalSpecialty::class, 'legal_specialty_id');
    }

    public function subject()
    {
        return $this->belongsTo(LegalSubject::class, 'legal_subject_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function installments()
    {
        return $this->hasMany(ConsultationInstallment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function case()
    {
        return $this->hasOne(CaseFile::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // 💰 total pagado
    public function getPaidAmountAttribute()
    {
        return $this->installments->sum(function ($i) {
            return $i->paid_amount;
        });
    }

    // 💸 saldo total
    public function getPendingAmountAttribute()
    {
        return $this->total_amount - $this->paid_amount;
    }

    // ✔ consulta pagada
    public function getIsPaidAttribute()
    {
        return $this->pending_amount <= 0;
    }

    // ❌ cuotas pendientes
    public function getHasPendingInstallmentsAttribute()
    {
        return $this->installments->contains(function ($i) {
            return !$i->is_paid;
        });
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeForEstablishment($query, $establishmentId)
    {
        return $query->where(
            'establishment_id',
            $establishmentId
        );
    }

    public function scopeForCurrentUser($query)
    {
        $user = auth()->user();

        if ($user->hasRole('Administrador')) {

            return $query;

        }

        return $query->where(
            'establishment_id',
            $user->employee->establishment_id
        );
    }

    public function scopeForLawyer($query)
    {
        $user = auth()->user();

        if ($user->hasRole('Abogado')) {

            return $query->where(
                'lawyer_id',
                $user->employee->id
            );

        }

        return $query;
    }
    
    public function scopeByUser($query, $user)
    {
        if (!$user->hasRole('Administrador')) {
            $query->where('establishment_id', $user->establishment_id);
        }
    }

    // ==========================================
    // ESTADO FINANCIERO
    // ==========================================

    public function getFinancialStatusAttribute(): string
    {
        if ($this->is_paid) {
            return 'paid';
        }

        // Existe alguna cuota vencida
        if ($this->installments->contains(function ($installment) {

            return !$installment->is_paid
                && $installment->due_date->isPast();

        })) {

            return 'overdue';

        }

        // Existe alguna cuota parcialmente pagada
        if ($this->installments->contains(function ($installment) {

            return $installment->paid_amount > 0
                && !$installment->is_paid;

        })) {

            return 'partial';

        }

        // Tiene cuotas pendientes pero ninguna vencida
        if ($this->has_pending_installments) {

            return 'current';

        }

        return 'no_installments';
    }

    // ==========================================
    // TEXTO ESTADO FINANCIERO
    // ==========================================

    public function getFinancialStatusLabelAttribute(): string
    {
        return match ($this->financial_status) {

            'paid' => 'Cancelado',

            'current' => 'Al día',

            'partial' => 'Pago parcial',

            'overdue' => 'Cuota vencida',

            default => 'Sin cuotas',
        };
    }

    // ==========================================
    // COLOR ESTADO FINANCIERO
    // ==========================================

    public function getFinancialStatusColorAttribute(): string
    {
        return match ($this->financial_status) {

            'paid' => 'primary',

            'current' => 'success',

            'partial' => 'warning',

            'overdue' => 'danger',

            default => 'secondary',
        };
    }

}