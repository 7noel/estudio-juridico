<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class Client extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

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

    public function getIsTemporaryDocumentAttribute(): bool
    {
        return str_starts_with((string) $this->document_number, 'TMP');
    }

    /*
    |--------------------------------------------------------------------------
    | DOCUMENTO TEMPORAL
    |--------------------------------------------------------------------------
    */

    public static function generateTemporaryDocumentNumber(?\DateTimeInterface $when = null): string
    {
        $when ??= now();

        return 'TMP'.$when->format('YmdHis');
    }

    public static function nextTemporaryDocumentNumber(): string
    {
        $base = static::generateTemporaryDocumentNumber();

        $number = $base;

        while (static::withTrashed()->where('document_number', $number)->exists()) {
            $number = $base.strtoupper(Str::random(2));
        }

        return $number;
    }

}
