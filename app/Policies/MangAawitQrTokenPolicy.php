<?php

namespace App\Policies;

use App\Models\MangAawitQrToken;
use App\Models\User;

class MangAawitQrTokenPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MangAawitQrToken $mangAawitQrToken): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MangAawitQrToken $mangAawitQrToken): bool
    {
        return true;
    }

    public function delete(User $user, MangAawitQrToken $mangAawitQrToken): bool
    {
        return true;
    }

    public function restore(User $user, MangAawitQrToken $mangAawitQrToken): bool
    {
        return true;
    }

    public function forceDelete(User $user, MangAawitQrToken $mangAawitQrToken): bool
    {
        return true;
    }
}