<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Shift;
use Illuminate\Auth\Access\Response;

class ShiftPolicy
{
    public function open(User $user)
    {
        // Kasir dan waiter bisa buka shift
        return $user->hasRole('kasir') || $user->hasRole('waiter') || $user->hasRole('manager');
    }

    public function close(User $user, Shift $shift)
    {
        // Hanya user yang membuka shift yang bisa tutup, atau manager/owner
        return $shift->user_id == $user->id || 
               $user->hasRole('manager') || 
               $user->hasRole('owner') ||
               $user->hasRole('super-admin');
    }

    public function view(User $user, Shift $shift)
    {
        // Kasir hanya lihat shift sendiri, manager/owner lihat semua
        return $shift->user_id == $user->id || 
               $user->hasRole('manager') || 
               $user->hasRole('owner') ||
               $user->hasRole('super-admin');
    }

    public function forceClose(User $user)
    {
        return $user->hasRole('manager') || $user->hasRole('owner') || $user->hasRole('super-admin');
    }
}
