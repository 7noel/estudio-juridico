<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use SoftDeletes;

    protected $appends = ['url'];

    protected $fillable = [

        'case_id',

        'document_type',

        'title',

        'file_path',
        'file_name',
        'file_size',

        'uploaded_by',

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

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }

}