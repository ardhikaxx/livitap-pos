<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Sale;
use Illuminate\Auth\Access\Response;

class SalePolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyPermission(['view-sales']);
    }

    public function view(User $user, Sale $sale)
    {
        // User bisa lihat sale jika di outlet yang sama atau memiliki role tertentu
        return $user->hasRole('super-admin') || 
               $user->hasRole('owner') ||
               ($user->hasRole('manager') && $sale->outlet_id == $user->primaryOutlet?->id) ||
               ($user->hasRole('kasir') && $sale->user_id == $user->id);
    }

    public function create(User $user)
    {
        return $user->can('create-sale');
    }

    public function void(User $user, Sale $sale)
    {
        // Hanya manager/owner bisa void, dan hanya di shift yang sama
        if ($user->hasRole('manager') || $user->hasRole('owner') || $user->hasRole('super-admin')) {
            return true;
        }
        
        return Response::deny('Hanya Manager/Owner yang bisa void transaksi.');
    }

    public function refund(User $user, Sale $sale)
    {
        return $user->can('process-refund');
    }
}
