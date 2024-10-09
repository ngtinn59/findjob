<?php

namespace App\Providers;


use App\Repositories\Recruitments\RecruitmentsRepository;
use App\Repositories\Recruitments\RecruitmentsRepositoryInterface;
use App\Services\Recruitments\RecruitmentsService;
use App\Services\Recruitments\RecruitmentsServiceInterface;
use App\Services\User\UserService;
use App\Services\User\UserServiceInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {


        //Recruitments
        $this->app->singleton(
            RecruitmentsRepositoryInterface::class,
            RecruitmentsRepository::class
        );

        $this->app->singleton(
            RecruitmentsServiceInterface::class,
            RecruitmentsService::class
        );




    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
