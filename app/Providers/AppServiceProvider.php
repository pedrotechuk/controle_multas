<?php

namespace App\Providers;

use App\Policies\AppsPolicy;
use App\Policies\UserPolicy;
use App\Policies\AdminPolicy;
use App\Policies\ProfilePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\PowerUsersPolicy;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Database\Schema\Blueprint;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void {}

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if (env('APP_ENV') === 'production') {
            URL::forceScheme('https');
        }

        URL::forceRootUrl(config('app.url'));

        Blueprint::macro('users_actions', function (): void {
            $this->string('created_by');
            $this->string('updated_by')->nullable();
            $this->string('deleted_by')->nullable();
        });

        // visao de adm
        Gate::define('admin.view-any', [AdminPolicy::class, 'viewAny']);


        // permissoes para alterar usuarios
        Gate::define('admin.users.view-any', [UserPolicy::class, 'viewAny']);
        Gate::define('admin.users.create', [UserPolicy::class, 'create']);
        Gate::define('admin.users.update', [UserPolicy::class, 'update']);
        Gate::define('admin.users.view', [UserPolicy::class, 'view']);
        Gate::define('admin.users.delete', [UserPolicy::class, 'delete']);
        Gate::define('admin.users.restore', [UserPolicy::class, 'restore']);

        // permissoes para alterar perfis
        Gate::define('admin.profile.view-any', [ProfilePolicy::class, 'viewAny']);
        Gate::define('admin.profile.create', [ProfilePolicy::class, 'create']);
        Gate::define('admin.profile.update', [ProfilePolicy::class, 'update']);
        Gate::define('admin.profile.view', [ProfilePolicy::class, 'view']);
        Gate::define('admin.profile.delete', [ProfilePolicy::class, 'delete']);
        Gate::define('admin.profile.restore', [ProfilePolicy::class, 'restore']);

        // permissoes para alterar permissoes
        Gate::define('admin.permission.view-any', [PermissionPolicy::class, 'viewAny']);
        Gate::define('admin.permission.create', [PermissionPolicy::class, 'create']);
        Gate::define('admin.permission.update', [PermissionPolicy::class, 'update']);
        Gate::define('admin.permission.view', [PermissionPolicy::class, 'view']);
        Gate::define('admin.permission.delete', [PermissionPolicy::class, 'delete']);
        Gate::define('admin.permission.restore', [PermissionPolicy::class, 'restore']);

        Gate::define('power_users.view-any', [PowerUsersPolicy::class, 'viewAny']);
        Gate::define('power_users.create', [PowerUsersPolicy::class, 'create']);
        Gate::define('power_users.update', [PowerUsersPolicy::class, 'update']);
        Gate::define('power_users.view', [PowerUsersPolicy::class, 'view']);
        Gate::define('power_users.delete', [PowerUsersPolicy::class, 'delete']);
        Gate::define('power_users.restore', [PowerUsersPolicy::class, 'restore']);

        Gate::define('apps.view-any', [AppsPolicy::class, 'viewAny']);
        Gate::define('apps.create', [AppsPolicy::class, 'create']);
        Gate::define('apps.update', [AppsPolicy::class, 'update']);
        Gate::define('apps.view', [AppsPolicy::class, 'view']);
        Gate::define('apps.delete', [AppsPolicy::class, 'delete']);
        Gate::define('apps.restore', [AppsPolicy::class, 'restore']);

    }
}
