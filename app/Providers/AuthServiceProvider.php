<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Permission;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        $permissions = Permission::get();
        if (!empty($permissions)) {

            foreach ($permissions as $permission) {
                Gate::define($permission->slug, function ($user) use ($permission) {
                    $permission_allowed = collect($user->role->permissions)->where('allow', true);
                    return $permission_allowed->pluck('type')->contains($permission->slug);
                });
            }

        } else {
            return false;
        }
    }
}
