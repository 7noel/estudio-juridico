<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConsultationInstallment extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'consultation_id',

        'installment_number',

        'amount',

        'due_date',

    ];

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

}