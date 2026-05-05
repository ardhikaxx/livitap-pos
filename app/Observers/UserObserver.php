<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user)
    {
        // Business ID is optional - no default needed
    }

    public function created(User $user)
    {
        // Ensure user has at least one outlet assigned
        if (!$user->outlets()->exists()) {
            $defaultOutlet = \App\Models\Outlet::first();
            if ($defaultOutlet) {
                $user->outlets()->attach($defaultOutlet->id, ['is_primary' => true]);
            }
        }
    }

    public function updating(User $user)
    {
        // Track changes untuk audit
    }
}
