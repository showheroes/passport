<?php

namespace ShowHeroes\Passport\Providers;

use ShowHeroes\Passport\Models\Teams\Team;
use ShowHeroes\Passport\Models\User;
use ShowHeroes\Passport\Policies\Users\TeamPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use ShowHeroes\Passport\Policies\Users\UserPolicy;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
        User::class => UserPolicy::class,
        'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication/authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (!$this->app->routesAreCached()) {
            Passport::routes();
            Passport::tokensCan(
                [
                    'user_info' => 'User information (name, team, email, avatar)',
                ]
            );
        }
    }
}
