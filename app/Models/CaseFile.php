<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseFile extends Model
{
    use SoftDeletes;

    protected $table = 'cases';

    protected $fillable = [

        'establishment_id',

        'consultation_id',

        'client_id',

        'lawyer_id',

        'slug',

        'case_number',

        'service_type',

        'legal_specialty_id',
        'legal_subject_id',

        'title',
        'description',

        'status',

        'opened_at',
        'closed_at',

        'created_by',

    ];

    protected $casts = [

        'opened_at' => 'datetime',
        'closed_at' => 'datetime',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function lawyer()
    {
        return $this->belongsTo(Employee::class, 'lawyer_id');
    }

    public function activities()
    {
        return $this->hasMany(LegalActivity::class, 'case_id');
    }

    public function agendaEvents()
    {
        return $this->hasMany(AgendaEvent::class);
    }

    public function communications()
    {
        return $this->hasMany(Communication::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }


    public function latestActivity()
    {
        return $this->hasOne(LegalActivity::class)->latestOfMany();
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }

    public function specialty()
    {
        return $this->belongsTo(LegalSpecialty::class, 'legal_specialty_id');
    }

    public function subject()
    {
        return $this->belongsTo(LegalSubject::class, 'legal_subject_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

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

    public function scopeVisible($query)
    {
        $user = auth()->user();

        if ($user->hasRole('Administrador')) {

            return $query;

        }

        if ($user->hasRole('Abogado')) {

            return $query->where(
                'lawyer_id',
                $user->employee->id
            );

        }

        return $query->where(
            'establishment_id',
            $user->employee->establishment_id
        );
    }

}