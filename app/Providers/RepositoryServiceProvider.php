<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Interfaces\GuaranteeInterface;
use App\Interfaces\FileInterface;
use App\Interfaces\ReviewInterface;
use App\Repositories\GuaranteeRepository;
use App\Repositories\FileRepository;
use App\Repositories\ReviewRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ReviewInterface::class, ReviewRepository::class);
        
        // GuaranteeRepository depends on ReviewRepository
        $this->app->bind(GuaranteeInterface::class, function ($app) {
            return new GuaranteeRepository(
                $app->make(ReviewRepository::class)
            );
        });
        
        // FileRepository depends on GuaranteeInterface
        $this->app->bind(FileInterface::class, function ($app) {
            return new FileRepository(
                $app->make(GuaranteeInterface::class)
            );
        });
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