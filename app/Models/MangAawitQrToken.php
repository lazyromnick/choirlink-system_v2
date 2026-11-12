<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MangAawitQrToken extends Model
{
    use HasFactory;

    protected $table = 'mang_aawit_qr_tokens';

    const UPDATED_AT = null;

    protected $fillable = [
        'mang_aawit_account_id',
        'token_hash',
        'created_by',
        'revoked_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'revoked_at' => 'datetime',
    ];

    public function mangAawitAccount()
    {
        return $this->belongsTo(MangAawitAccount::class, 'mang_aawit_account_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}