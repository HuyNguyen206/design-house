<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
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
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Gate::define('delete-invitation', function(User $user, $invitation) {
            if ($user->id !== $invitation->sender_id) {
                throw new AuthorizationException('You are not the person who send this invitation');
            }
            return true;
        });

        Gate::define('respond-invitation', function(User $user, $invitation) {
             if ($user->email !== $invitation->recipient_email) {
                 throw new AuthorizationException('You are not the person who receive this invitation');
             }
             return true;
        });

        //
    }
}
