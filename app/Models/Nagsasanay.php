<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Nagsasanay extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'nagsasanay';

    protected $fillable = [
        'mang_aawit_id',
        'training_track',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'birthday',
        'sex',
        'address',
        'date_registered',
        'mang_aawit_record_id',
        'training_category',
        'training_start_date',
        'trainer_name',
        'progress_stage',
        'training_status',
        'remarks',
        'membership_classification',
        'mang_aawit_in',
        'previous_locale',
        'destination_locale',
        'transfer_date',
        'purok_grupo_id',
    ];

    protected $casts = [
        'birthday'            => 'date',
        'date_registered'     => 'date',
        'training_start_date' => 'date',
        'transfer_date'       => 'date',
        'deleted_at'          => 'datetime',
    ];

    // For Organista track — the linked mang-aawit
    public function mangAawit()
    {
        return $this->belongsTo(MangAawit::class, 'mang_aawit_record_id');
    }

    // Display name — works for both tracks
    public function getFullNameAttribute(): string
    {
        if ($this->training_track === 'Organista' && $this->mangAawit) {
            return $this->mangAawit->full_name;
        }

        $name = $this->last_name . ', ' . $this->first_name;
        if ($this->middle_name) $name .= ' ' . $this->middle_name;
        if ($this->suffix) $name .= ' ' . $this->suffix;
        return $name;
    }

    public function getAgeAttribute(): ?int
    {
        $birthday = $this->training_track === 'Organista'
            ? $this->mangAawit?->birthday
            : $this->birthday;

        return $birthday?->age;
    }

    public function isCompleted(): bool
    {
        return $this->progress_stage === 'Completed';
    }

    public function isDiscontinued(): bool
    {
        return $this->progress_stage === 'Discontinued';
    }

    public function purokGrupo()
    {
        return $this->belongsTo(PurokGrupo::class, 'purok_grupo_id');
    }
}