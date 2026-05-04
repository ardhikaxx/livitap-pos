<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasPrimaryOutlet
{
    /**
     * Get user's primary outlet
     */
    public function primaryOutlet()
    {
        return $this->belongsToMany(Outlet::class, 'outlet_user')
            ->where('is_primary', true)
            ->first();
    }

    /**
     * Get all outlets with primary flag
     */
    public function outletsWithPivot()
    {
        return $this->outlets()->withPivot('is_primary')->get();
    }
}
