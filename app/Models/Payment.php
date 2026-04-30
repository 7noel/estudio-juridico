<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'consultation_id',
        'consultation_installment_id',

        'amount',

        'payment_date',

        'payment_method',

        'reference',

        'created_by',

    ];

    protected $casts = [

        'payment_date' => 'date',

    ];

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function installment()
    {
        return $this->belongsTo(
            ConsultationInstallment::class,
            'consultation_installment_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */
    
    public function creator()
    {
        return $this->belongsTo(
            User::class,
            'created_by'
        );
    }

}