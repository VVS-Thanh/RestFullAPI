<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        Passport::ignoreRoutes();

        Passport::tokensExpireIn(Carbon::now()->addMinutes(60));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(7));


        //
    }
}
