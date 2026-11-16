<?php

namespace App\Policies;

use App\Models\MangAawit;
use App\Models\User;

class MangAawitPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MangAawit $mangAawit): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MangAawit $mangAawit): bool
    {
        return true;
    }

    public function delete(User $user, MangAawit $mangAawit): bool
    {
        return true;
    }

    public function restore(User $user, MangAawit $mangAawit): bool
    {
        return true;
    }

    public function forceDelete(User $user, MangAawit $mangAawit): bool
    {
        return true;
    }
}