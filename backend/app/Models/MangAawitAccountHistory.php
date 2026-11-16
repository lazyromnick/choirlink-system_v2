<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MangAawitAccountHistory extends Model
{
    use HasFactory;

    protected $table = 'mang_aawit_account_histories';

    const UPDATED_AT = null;

    protected $fillable = [
        'mang_aawit_account_id',
        'event_type',
        'previous_status',
        'new_status',
        'reason',
        'changed_by',
        'actor_type',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function mangAawitAccount()
    {
        return $this->belongsTo(MangAawitAccount::class, 'mang_aawit_account_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}