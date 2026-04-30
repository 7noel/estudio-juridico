<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Communication extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'consultation_id',
        'case_id',

        'communication_type',

        'title',

        'description',

        'communication_datetime',

        'created_by',

    ];

    protected $casts = [

        'communication_datetime' => 'datetime',

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

    public function case()
    {
        return $this->belongsTo(CaseFile::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function establishment()
    {
        return $this->case
            ? $this->case->establishment()
            : null;
    }

}