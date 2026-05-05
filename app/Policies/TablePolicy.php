<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Table;
use Illuminate\Auth\Access\Response;

class TablePolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('waiter') || $user->hasRole('manager') || $user->hasRole('kasir');
    }

    public function view(User $user, Table $table)
    {
        return true;
    }

    public function update(User $user, Table $table)
    {
        // Kasir, waiter, manager bisa update status meja
        return $user->hasAnyRole(['kasir', 'waiter', 'manager']);
    }

    public function delete(User $user, Table $table)
    {
        return $user->hasRole('manager') || $user->hasRole('super-admin');
    }
}
