<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Business;
use Illuminate\Auth\Access\Response;

class BusinessPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasRole('super-admin') || $user->hasRole('owner');
    }

    public function view(User $user, Business $business)
    {
        return $user->hasRole('super-admin') || 
               ($user->hasRole('owner') && $user->business_id && $business->id == $user->business_id);
    }

    public function create(User $user)
    {
        return $user->hasRole('super-admin') || $user->hasRole('owner');
    }

    public function update(User $user, Business $business)
    {
        return $user->hasRole('super-admin') || 
               ($user->hasRole('owner') && $user->business_id && $business->id == $user->business_id);
    }

    public function delete(User $user, Business $business)
    {
        return $user->hasRole('super-admin');
    }
}
