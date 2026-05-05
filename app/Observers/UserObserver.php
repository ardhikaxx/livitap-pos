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
        // User created hook
    }

    public function updating(User $user)
    {
        // Track changes untuk audit
    }
}
