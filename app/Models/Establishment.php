<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Establishment extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'name',
        'ruc',
        'address',
        'ubigeo_code',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function cases()
    {
        return $this->hasMany(CaseFile::class);
    }

}