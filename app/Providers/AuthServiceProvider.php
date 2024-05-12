<?php

namespace App\Providers;

use App\Models\OneBssAccount;
use App\Models\OneBssCustomer;
use App\Models\User;
use App\Policies\Admin\UserPolicy;
use App\Policies\OneBssAccountPolicy;
use App\Policies\OneBssCustomerPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        OneBssAccount::class => OneBssAccountPolicy::class,
        OneBssCustomer::class => OneBssCustomerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /* Gate::before(function ($user, $ability) {
            return $user->is_admin ? true : null;
        }); */
    }
}
