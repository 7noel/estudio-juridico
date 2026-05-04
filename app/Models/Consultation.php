<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Consultation extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'establishment_id',
        'client_id',

        'service_type',

        'legal_specialty_id',
        'legal_subject_id',

        'lawyer_id',

        'title',
        'description',

        'total_amount',

        'status',

        'created_by',

        'assigned_at',
        'evaluated_at',

    ];

    protected $casts = [

        'assigned_at' => 'datetime',
        'evaluated_at' => 'datetime',

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

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function specialty()
    {
        return $this->belongsTo(LegalSpecialty::class, 'legal_specialty_id');
    }

    public function subject()
    {
        return $this->belongsTo(LegalSubject::class, 'legal_subject_id');
    }

    public function lawyer()
    {
        return $this->belongsTo(User::class, 'lawyer_id');
    }

    public function installments()
    {
        return $this->hasMany(ConsultationInstallment::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function case()
    {
        return $this->hasOne(CaseFile::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */

    public function scopeForEstablishment($query, $establishmentId)
    {
        return $query->where(
            'establishment_id',
            $establishmentId
        );
    }

    public function scopeForCurrentUser($query)
    {
        $user = auth()->user();

        if ($user->hasRole('Administrador')) {

            return $query;

        }

        return $query->where(
            'establishment_id',
            $user->employee->establishment_id
        );
    }

    public function scopeForLawyer($query)
    {
        $user = auth()->user();

        if ($user->hasRole('Abogado')) {

            return $query->where(
                'lawyer_id',
                $user->employee->id
            );

        }

        return $query;
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES
    |--------------------------------------------------------------------------
    */
    
    public function scopeByUser($query, $user)
    {
        if (!$user->hasRole('Administrador')) {
            $query->where('establishment_id', $user->establishment_id);
        }
    }

}