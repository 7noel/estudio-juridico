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

    public function ubigeo()
    {
        return $this->belongsTo(
            \App\Models\Ubigeo::class,
            'ubigeo_code',
            'code'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ACCESORS
    |--------------------------------------------------------------------------
    */

    public function getUbigeoTextAttribute()
    {
        if (!$this->ubigeo) {
            return null;
        }

        return
            $this->ubigeo->departamento
            .' - '.
            $this->ubigeo->provincia
            .' - '.
            $this->ubigeo->distrito;
    }
    
    public function getDocumentTypeTextAttribute()
    {
        return config('options.client_document_types')[$this->document_type]
            ?? $this->document_type;
    }

}