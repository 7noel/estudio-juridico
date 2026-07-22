<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
//use OwenIt\Auditing\Contracts\Auditable;

//class ConsultationFollowUp extends Model implements Auditable
class ConsultationFollowUp extends Model
{
    use SoftDeletes;
    //use \OwenIt\Auditing\Auditable;

    protected $fillable = [
        'consultation_id',
        'user_id',
        'contact_date',
        'communication_type',
        'result',
        'next_contact_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'contact_date' => 'date',
            'next_contact_date' => 'date',
        ];
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function followUps()
    {
        return $this->hasMany(ConsultationFollowUp::class)
            ->latest('contact_date')
            ->latest('id');
    }

    public function lastFollowUp()
    {
        return $this->hasOne(ConsultationFollowUp::class)
            ->ofMany([
                'contact_date' => 'max',
                'id' => 'max',
            ]);
    }

    public function getCommunicationTypeLabelAttribute(): string
    {
        return config('options.communication_types')[$this->communication_type]
            ?? $this->communication_type;
    }

    public function getResultLabelAttribute(): string
    {
        return config('options.follow_up_results')[$this->result]
            ?? $this->result;
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->next_contact_date?->isBefore(today()) ?? false;
    }

    public function getDaysSinceContactAttribute(): ?int
    {
        return $this->contact_date?->diffInDays(today());
    }

    public function getDaysUntilNextContactAttribute(): ?int
    {
        return $this->next_contact_date?->diffInDays(today(), false);
    }



}