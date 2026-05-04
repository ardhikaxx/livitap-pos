<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Model;

trait HasBusiness
{
    public function scopeOfBusiness($query, $businessId)
    {
        return $query->where('business_id', $businessId);
    }
}
