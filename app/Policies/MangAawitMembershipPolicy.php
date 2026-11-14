<?php

namespace App\Policies;

use App\Models\MangAawitMembership;
use App\Models\User;

class MangAawitMembershipPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MangAawitMembership $mangAawitMembership): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MangAawitMembership $mangAawitMembership): bool
    {
        return true;
    }

    public function delete(User $user, MangAawitMembership $mangAawitMembership): bool
    {
        return true;
    }

    public function restore(User $user, MangAawitMembership $mangAawitMembership): bool
    {
        return true;
    }

    public function forceDelete(User $user, MangAawitMembership $mangAawitMembership): bool
    {
        return true;
    }
}