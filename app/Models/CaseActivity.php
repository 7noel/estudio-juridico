<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CaseActivity extends Model
{
    use SoftDeletes;
    
    protected $fillable = [
        'case_id',
        'type',
        'subtype',
        'title',
        'description',
        'activity_at',
        'created_by',
    ];

    protected $casts = [
        'activity_at' => 'datetime',
    ];

    public function case()
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function agendaEvent()
    {
        return $this->hasOne(
            AgendaEvent::class,
            'case_activity_id'
        );
    }
}