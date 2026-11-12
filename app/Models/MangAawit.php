<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MangAawit extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'mang_aawits';

    protected $fillable = [
        'mang_aawit_id',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'birthday',
        'sex',
        'address',
        'date_registered',
        'purok_grupo_id',
    ];

    // mang_aawit_id is NOT in fillable — assigned only by the ID generator service

    protected $casts = [
        'birthday'        => 'date',
        'date_registered' => 'date',
        'deleted_at'      => 'datetime',
    ];

    // Relationships
    public function memberships()
    {
        return $this->hasMany(MangAawitMembership::class, 'mang_aawit_record_id');
    }

    public function currentMembership()
    {
        return $this->hasOne(MangAawitMembership::class, 'mang_aawit_record_id')
                    ->where('is_current', true);
    }

    public function account()
    {
        return $this->hasOne(MangAawitAccount::class, 'mang_aawit_record_id');
    }

    // Computed attribute — age is never stored
    public function getAgeAttribute(): ?int
    {
        return $this->birthday?->age;
    }

    // Display format: Last Name, First Name Middle Name Suffix
    public function getFullNameAttribute(): string
    {
        $name = $this->last_name . ', ' . $this->first_name;
        if ($this->middle_name) $name .= ' ' . $this->middle_name;
        if ($this->suffix) $name .= ' ' . $this->suffix;
        return $name;
    }

    public function purokGrupo()
    {
        return $this->belongsTo(PurokGrupo::class, 'purok_grupo_id');
    }
}