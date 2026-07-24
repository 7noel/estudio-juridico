<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'establishment_id',
        'consultation_id',
        'consultation_installment_id',
        'amount',
        'payment_date',
        'payment_method',
        'reference',
        'description',
        'user_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
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

    public function installment()
    {
        return $this->belongsTo(ConsultationInstallment::class, 'consultation_installment_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
}