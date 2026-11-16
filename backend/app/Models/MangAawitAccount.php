<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class MangAawitAccount extends Authenticatable
{
    use HasFactory;

    protected $table = 'mang_aawit_accounts';

    protected $fillable = [
        'mang_aawit_record_id',
        'password',
        'account_status',
        'must_change_password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'must_change_password' => 'boolean',
    ];

    // Auto-hash password when setting it
    public function setPasswordAttribute(string $value): void
    {
        $this->attributes['password'] = Hash::needsRehash($value)
            ? Hash::make($value)
            : $value;
    }

    public function mangAawit()
    {
        return $this->belongsTo(MangAawit::class, 'mang_aawit_record_id');
    }

    public function histories()
    {
        return $this->hasMany(MangAawitAccountHistory::class, 'mang_aawit_account_id');
    }

    public function qrTokens()
    {
        return $this->hasMany(MangAawitQrToken::class, 'mang_aawit_account_id');
    }

    public function activeQrToken()
    {
        return $this->hasOne(MangAawitQrToken::class, 'mang_aawit_account_id')
                    ->whereNull('revoked_at');
    }
}