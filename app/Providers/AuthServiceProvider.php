<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
    ];
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);
        $gate->define('roles', function ($user, $post) {
            return $user->id === $post->user_id;
        });
    }
}
