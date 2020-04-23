<?php

namespace App\Providers;

use App\Unit;
use App\Item;
use App\InvoiceDetail;
use App\Policies\UnitPolicy;
use App\Policies\ItemPolicy;
use App\Policies\InvoiceDetailPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Unit::class => UnitPolicy::class,
        Item::class => ItemPolicy::class,
        InvoiceDetail::class => InvoiceDetailPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
