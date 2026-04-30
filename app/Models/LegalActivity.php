<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LegalActivity extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'case_id',

        'activity_type',

        'title',

        'description',

        'activity_date',

        'created_by',

    ];

    protected $casts = [

        'activity_date' => 'date',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function case()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function agendaEvents()
    {
        return $this->hasMany(AgendaEvent::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}