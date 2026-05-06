<?php

namespace App\Providers;

use App\Models\Business;
use App\Models\Customer;
use App\Models\Discount;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Table;
use App\Policies\BusinessPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\DiscountPolicy;
use App\Policies\ProductPolicy;
use App\Policies\SalePolicy;
use App\Policies\TablePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        // Models => Policies
        Business::class => BusinessPolicy::class,
        Product::class  => ProductPolicy::class,
        Sale::class     => SalePolicy::class,
        Customer::class => CustomerPolicy::class,
        Table::class    => TablePolicy::class,
        Discount::class => DiscountPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}
