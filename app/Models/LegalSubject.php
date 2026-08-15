<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class LegalSubject extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [

        'legal_specialty_id',
        'name',

    ];

    public function specialty()
    {
        return $this->belongsTo(LegalSpecialty::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'legal_subject_id');
    }

}