<?php

namespace App\Policies;

use App\Models\MangAawitAccount;
use App\Models\User;

class MangAawitAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MangAawitAccount $mangAawitAccount): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MangAawitAccount $mangAawitAccount): bool
    {
        return true;
    }

    public function delete(User $user, MangAawitAccount $mangAawitAccount): bool
    {
        return true;
    }

    public function restore(User $user, MangAawitAccount $mangAawitAccount): bool
    {
        return true;
    }

    public function forceDelete(User $user, MangAawitAccount $mangAawitAccount): bool
    {
        return true;
    }
}