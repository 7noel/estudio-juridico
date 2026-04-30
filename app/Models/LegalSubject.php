<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalSubject extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'legal_specialty_id',
        'name',

    ];

    public function specialty()
    {
        return $this->belongsTo(LegalSpecialty::class);
    }

}