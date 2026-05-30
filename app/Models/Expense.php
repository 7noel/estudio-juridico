<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'establishment_id',
        'case_id',
        'user_id',
        'category',
        'amount',
        'expense_date',
        'payment_method',
        'reference',
        'description',
        'attachment',

    ];

    protected $casts = [

        'expense_date' => 'date',

    ];

    /*
    |--------------------------------------------------------------------------
    | RELATIONS
    |--------------------------------------------------------------------------
    */

    public function case()
    {
        return $this->belongsTo(CaseFile::class, 'case_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function establishment()
    {
        return $this->belongsTo(Establishment::class);
    }
}