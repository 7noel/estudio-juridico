<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class LegalSpecialty extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [

        'name',

    ];

    public function subjects()
    {
        return $this->hasMany(LegalSubject::class);
    }

}