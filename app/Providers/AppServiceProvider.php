<?php

namespace App\Providers;

use App\Core\Data\Services\StoreCommentService;
use App\Core\Data\Services\StoreTaskService;
use App\Core\Domain\UseCases\StoreCommentUseCase;
use App\Core\Domain\UseCases\StoreTaskUseCase;
use App\Core\Infra\Repositories\EloquentCommentRepository;
use App\Core\Infra\Repositories\EloquentTaskRepository;
use App\Core\Infra\Repositories\EloquentUserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StoreTaskUseCase::class, function ($app) {
            return new StoreTaskService(
                userRepository: $app->make(EloquentUserRepository::class),
                taskRepository: $app->make(EloquentTaskRepository::class)
            );
        });

        $this->app->singleton(StoreCommentUseCase::class, function ($app) {
            return new StoreCommentService(
                userRepository: $app->make(EloquentUserRepository::class),
                taskRepository: $app->make(EloquentTaskRepository::class),
                commentRepository: $app->make(EloquentCommentRepository::class)
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
