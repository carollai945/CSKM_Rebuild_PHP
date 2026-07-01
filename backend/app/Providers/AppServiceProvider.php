<?php

namespace App\Providers;

use App\Enums\Role;
use App\Models\User;
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
        Gate::define('is-admin', fn (User $user) => $user->role === Role::Admin);

        Gate::define('is-ceo', fn (User $user) => $user->role === Role::CEO);

        Gate::define('is-regmgr', fn (User $user) => $user->role === Role::RegMgr);

        Gate::define('is-finance', fn (User $user) => $user->role === Role::Finance);

        Gate::define('is-teacher', fn (User $user) => $user->role === Role::Teacher);

        Gate::define('is-staff', fn (User $user) => $user->role === Role::Staff);

        Gate::define('management', fn (User $user) => in_array($user->role, [
            Role::Admin,
            Role::CEO,
            Role::RegMgr,
        ], strict: true));
    }
}
