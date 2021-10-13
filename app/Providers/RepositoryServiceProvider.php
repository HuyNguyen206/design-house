<?php

namespace App\Providers;

use App\Repositories\Contracts\CommentInterface;
use App\Repositories\Contracts\DesignInterface;
use App\Repositories\Contracts\UserInterface;
use App\Repositories\Eloquent\CommentRepository;
use App\Repositories\Eloquent\DesignRepository;
use App\Repositories\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public $bindings = [
        DesignInterface::class => DesignRepository::class,
        UserInterface::class => UserRepository::class,
        CommentInterface::class => CommentRepository::class,
    ];
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //

    }
}
