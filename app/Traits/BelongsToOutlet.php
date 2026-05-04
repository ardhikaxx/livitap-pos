<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait BelongsToOutlet
{
    public function scopeOfOutlet($query, $outletId)
    {
        return $query->where('outlet_id', $outletId);
    }

    public function scopeForUser($query, $user)
    {
        if ($user->hasRole('super-admin')) {
            return $query;
        }

        $outletIds = $user->outlets()->pluck('outlets.id');
        return $query->whereIn('outlet_id', $outletIds);
    }
}
