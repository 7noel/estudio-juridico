<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'document_type',
        'document_number',

        'full_name',

        'address',
        'ubigeo_code',

        'mobile',
        'phone',
        'email',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function cases()
    {
        return $this->hasMany(CaseFile::class);
    }

}