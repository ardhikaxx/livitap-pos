<?php

namespace App\Observers;

use App\Models\User;

class UserObserver
{
    public function creating(User $user)
    {
        // Set default business_id from first outlet if not set
        if (!$user->business_id && $user->outlets()->exists()) {
            $primaryOutlet = $user->outlets()->where('is_primary', true)->first();
            if ($primaryOutlet) {
                $user->business_id = $primaryOutlet->business_id;
            }
        }
    }

    public function created(User $user)
    {
        // Ensure user has at least one outlet assigned
        if (!$user->outlets()->exists() && $user->business_id) {
            $defaultOutlet = \App\Models\Outlet::where('business_id', $user->business_id)->first();
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
