<?php

namespace App\Policies;

use App\Models\Nagsasanay;
use App\Models\User;

class NagsasanayPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Nagsasanay $nagsasanay): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Nagsasanay $nagsasanay): bool
    {
        return true;
    }

    public function delete(User $user, Nagsasanay $nagsasanay): bool
    {
        return true;
    }

    public function restore(User $user, Nagsasanay $nagsasanay): bool
    {
        return true;
    }

    public function forceDelete(User $user, Nagsasanay $nagsasanay): bool
    {
        return true;
    }
}