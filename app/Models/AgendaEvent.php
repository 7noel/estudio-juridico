<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Auditable as AuditableTrait;

class AgendaEvent extends Model implements Auditable
{
    use SoftDeletes, AuditableTrait;

    protected $fillable = [

        'case_id',

        'case_activity_id',

        'type',

        'title',

        'description',

        'start_datetime',

        'end_datetime',

        'location',

        'user_id',

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
            CaseActivity::class,
            'case_activity_id'
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function establishment()
    {
        return $this->case
            ? $this->case->establishment()
            : null;
    }

    public function getIsOverdueAttribute()
    {
        return $this->start_datetime < now();
    }

    public function getIsTodayAttribute()
    {
        return $this->start_datetime->isToday();
    }

}