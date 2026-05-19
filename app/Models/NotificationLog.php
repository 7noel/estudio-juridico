<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationLog extends Model
{
    protected $fillable = [

        'type',
        'related_id',
        'phone',
        'sent_at',

    ];
}