<?php

namespace CodeShopping\Providers;

use Illuminate\Http\Request;
use CodeShopping\Models\User;
use CodeShopping\Models\Product;
use CodeShopping\Models\Category;
use Illuminate\Support\Facades\Route;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use CodeShopping\Common\OnlyTrashed;
use CodeShopping\Models\ChatGroupInvitation;

class RouteServiceProvider extends ServiceProvider
{
    use OnlyTrashed;
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'CodeShopping\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
        Route::bind('category', function ($value) {
            return Category::whereId($value)->orWhere('slug',$value)->firstOrFail();
        });
        Route::bind('product', function ($value) {
            $query = Product::query();
            $request = app(Request::class);
            $query = $this->onlyTrashedIfRequest($request,$query);
            return $query->whereId($value)->orWhere('slug',$value)->firstOrFail();
        });
        Route::bind('user', function ($value) {
            $query = User::query();
            $request = app(Request::class);
            $query = $this->onlyTrashedIfRequest($request,$query);
            return $query->whereId($value)->firstOrFail();
        });

        Route::bind('invitation_slug', function ($value) {
            return ChatGroupInvitation::where('slug',$value)->first();
        });
    }


    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
