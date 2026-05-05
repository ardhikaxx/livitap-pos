<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Customer;
use Illuminate\Auth\Access\Response;

class CustomerPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyPermission(['view-customer', 'create-customer', 'edit-customer', 'delete-customer']);
    }

    public function view(User $user, Customer $customer)
    {
        return $user->hasAnyPermission(['view-customer', 'edit-customer']);
    }

    public function create(User $user)
    {
        return $user->can('create-customer');
    }

    public function update(User $user, Customer $customer)
    {
        return $user->can('edit-customer');
    }

    public function delete(User $user, Customer $customer)
    {
        if ($customer->sales()->exists()) {
            return Response::deny('Pelanggan tidak bisa dihapus karena memiliki riwayat transaksi.');
        }
        return $user->can('delete-customer');
    }
}
