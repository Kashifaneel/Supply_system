<?php

namespace App\Providers;

use App\Models\PurchaseOrder;
use App\Models\Supply;
use App\Models\Payment;
use App\Policies\PurchaseOrderPolicy;
use App\Policies\SupplyPolicy;
use App\Policies\PaymentPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        Gate::policy(PurchaseOrder::class, PurchaseOrderPolicy::class);
        Gate::policy(Supply::class, SupplyPolicy::class);
        Gate::policy(Payment::class, PaymentPolicy::class);
    }
}
