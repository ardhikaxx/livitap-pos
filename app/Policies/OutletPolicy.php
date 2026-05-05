<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Outlet;
use Illuminate\Auth\Access\Response;

class OutletPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('super-admin') || $user->hasRole('owner') || $user->hasRole('manager');
    }

    public function view(User $user, Outlet $outlet)
    {
        return $user->hasRole('super-admin') || 
               $user->hasRole('owner') && $user->business_id && $outlet->business_id == $user->business_id ||
               $user->hasRole('manager') && $outlet->id == $user->primaryOutlet?->id;
    }

    public function create(User $user)
    {
        return $user->hasRole('owner') || $user->hasRole('super-admin');
    }

    public function update(User $user, Outlet $outlet)
    {
        return $user->hasRole('owner') || $user->hasRole('super-admin');
    }

    public function delete(User $user, Outlet $outlet)
    {
        return $user->hasRole('super-admin');
    }
}
