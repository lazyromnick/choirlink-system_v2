<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurokGrupo extends Model
{
    use HasFactory;

    protected $table = 'purok_grupo';

    protected $fillable = [
        'name',
        'description',
    ];

    public function mangAawits()
    {
        return $this->hasMany(MangAawit::class, 'purok_grupo_id');
    }

    public function nagsasanay()
    {
        return $this->hasMany(Nagsasanay::class, 'purok_grupo_id');
    }
}