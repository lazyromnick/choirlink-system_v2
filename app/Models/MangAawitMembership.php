<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MangAawitMembership extends Model
{
    use HasFactory;

    protected $table = 'mang_aawit_memberships';

    protected $fillable = [
        'mang_aawit_record_id',
        'start_date',
        'end_date',
        'is_current',
        'membership_classification',
        'designation',
        'tungkulin',
        'tungkulin_status',
        'membership_status',
        'mang_aawit_in',
        'previous_locale',
        'destination_locale',
        'transfer_date',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'transfer_date' => 'date',
        'is_current'    => 'boolean',
    ];

    public function mangAawit()
    {
        return $this->belongsTo(MangAawit::class, 'mang_aawit_record_id');
    }
}