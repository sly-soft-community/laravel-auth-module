<?php

namespace Modules\Auth\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Routing\Router;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var string $moduleName
     */
    protected $moduleName = 'Auth';

    /**
     * @var string $moduleNameLower
     */
    protected $moduleNameLower = 'auth';

    /**
     * The application's route middleware groups.
     *
     * @var array<string, array<int, class-string|string>>
     */
    protected $middlewareGroups = [
        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ],
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerMiddlewareGroups($this->app['router']);
        $this->registerConfig();

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(RouteServiceProvider::class);
    }

    /**
     * Register config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/cors.php'), 'cors'
        );
        $this->mergeConfigFrom(
            module_path($this->moduleName, 'Config/sanctum.php'), 'sanctum'
        );
    }

    /**
     * Register the middlewares.
     *
     * @param  Router $router
     * @return void
     */
    public function registerMiddlewareGroups(Router $router)
    {
        foreach ($this->middlewareGroups as $group => $middlewares) {
            foreach ($middlewares as $middleware) {
                $router->pushMiddlewareToGroup($group, $middleware);
            }
        }
    }
}
