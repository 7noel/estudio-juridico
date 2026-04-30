<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalSpecialty extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'name',

    ];

    public function subjects()
    {
        return $this->hasMany(LegalSubject::class);
    }

}