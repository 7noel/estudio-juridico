<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationInstallment extends Model
{
    use SoftDeletes;

    protected $fillable = ['establishment_id', 'consultation_id', 'installment_number', 'amount', 'due_date', 'paid_at', 'paid_amount'];

    protected $casts = [
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // 💰 total pagado
    // public function getPaidAmountAttribute()
    // {
    //     return $this->payments()->sum('amount');
    // }

    // 💸 saldo pendiente
    public function getPendingAmountAttribute()
    {
        return round($this->amount - $this->paid_amount, 2);
    }

    // ✔ pagado completamente
    public function getIsPaidAttribute()
    {
        return $this->pending_amount <= 0;
    }

}