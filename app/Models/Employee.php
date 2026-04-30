<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'establishment_id',
        'user_id',

        'full_name',

        'document_type',
        'document_number',

        'mobile',
        'phone',
        'email',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'lawyer_id');
    }

    public function cases()
    {
        return $this->hasMany(CaseFile::class, 'lawyer_id');
    }

    public function createdConsultations()
    {
        return $this->hasMany(Consultation::class, 'created_by');
    }

    public function createdCases()
    {
        return $this->hasMany(CaseFile::class, 'created_by');
    }

}