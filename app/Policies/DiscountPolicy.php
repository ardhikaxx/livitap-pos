<?php

namespace App\Http\Policies;

use App\Models\User;
use App\Models\Discount;
use Illuminate\Auth\Access\Response;

class DiscountPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyPermission(['view-discount', 'create-discount', 'edit-discount', 'delete-discount']);
    }

    public function view(User $user, Discount $discount)
    {
        return $user->hasAnyPermission(['view-discount', 'edit-discount']);
    }

    public function create(User $user)
    {
        return $user->can('create-discount');
    }

    public function update(User $user, Discount $discount)
    {
        return $user->can('edit-discount');
    }

    public function delete(User $user, Discount $discount)
    {
        return $user->can('delete-discount');
    }
}
