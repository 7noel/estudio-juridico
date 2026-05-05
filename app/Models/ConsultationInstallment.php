<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationInstallment extends Model
{
    use SoftDeletes;

    protected $fillable = ['consultation_id', 'installment_number', 'amount', 'due_date'];

    protected $casts = [
        'due_date' => 'date',
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
    public function getPaidAmountAttribute()
    {
        return $this->payments()->sum('amount');
    }

    // 💸 saldo pendiente
    public function getPendingAmountAttribute()
    {
        return $this->amount - $this->paid_amount;
    }

    // ✔ pagado completamente
    public function getIsPaidAttribute()
    {
        return $this->pending_amount <= 0;
    }

}