<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AgendaEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'case_id',

        'legal_activity_id',

        'title',

        'description',

        'start_datetime',

        'end_datetime',

        'location',

        'created_by',

    ];

    protected $casts = [

        'start_datetime' => 'datetime',

        'end_datetime' => 'datetime',

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

    public function activity()
    {
        return $this->belongsTo(
            LegalActivity::class,
            'legal_activity_id'
        );
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