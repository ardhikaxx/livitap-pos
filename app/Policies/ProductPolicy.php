<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use Illuminate\Auth\Access\Response;

class ProductPolicy
{
    public function viewAny(User $user)
    {
        return $user->hasAnyPermission(['view-product', 'create-product', 'edit-product', 'delete-product']);
    }

    public function view(User $user, Product $product)
    {
        return $user->hasAnyPermission(['view-product', 'edit-product', 'delete-product']);
    }

    public function create(User $user)
    {
        return $user->can('create-product');
    }

    public function update(User $user, Product $product)
    {
        return $user->can('edit-product');
    }

    public function delete(User $user, Product $product)
    {
        // Hanya bisa hapus jika tidak ada transaksi terkait
        if ($product->saleItems()->exists() || $product->purchaseOrderItems()->exists()) {
            return Response::deny('Produk tidak bisa dihapus karena sudah digunakan dalam transaksi.');
        }
        return $user->can('delete-product');
    }
}
