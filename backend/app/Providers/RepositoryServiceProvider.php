<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Interfaces\ArticleRepositoryInterface;
use App\Repositories\ArticleRepository;
use App\Repositories\Interfaces\CategoryRepositoryInterface;
use App\Repositories\CategoryRepository;
use App\Repositories\Interfaces\WriterRepositoryInterface;
use App\Repositories\WriterRepository;
use App\Repositories\Interfaces\OpinionRepositoryInterface;
use App\Repositories\OpinionRepository;
use App\Repositories\Interfaces\VideoRepositoryInterface;
use App\Repositories\VideoRepository;
use App\Repositories\Interfaces\HomepageSectionRepositoryInterface;
use App\Repositories\HomepageSectionRepository;
use App\Repositories\Interfaces\AdvertisementRepositoryInterface;
use App\Repositories\AdvertisementRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Article Repository
        $this->app->bind(
            ArticleRepositoryInterface::class,
            ArticleRepository::class
        );

        // Bind Category Repository
        $this->app->bind(
            CategoryRepositoryInterface::class,
            CategoryRepository::class
        );

        // Bind User Repository
        $this->app->bind(
            \App\Repositories\Interfaces\UserRepositoryInterface::class,
            \App\Repositories\UserRepository::class
        );

        // Bind Role Repository
        $this->app->bind(
            \App\Repositories\Interfaces\RoleRepositoryInterface::class,
            \App\Repositories\RoleRepository::class
        );

        // Bind Permission Repository
        $this->app->bind(
            \App\Repositories\Interfaces\PermissionRepositoryInterface::class,
            \App\Repositories\PermissionRepository::class
        );

        // Bind Writer Repository
        $this->app->bind(
            WriterRepositoryInterface::class,
            WriterRepository::class
        );

        // Bind Opinion Repository
        $this->app->bind(
            OpinionRepositoryInterface::class,
            OpinionRepository::class
        );

        // Bind Video Repository
        $this->app->bind(
            VideoRepositoryInterface::class,
            VideoRepository::class
        );

        // Bind Homepage Section Repository
        $this->app->bind(
            HomepageSectionRepositoryInterface::class,
            HomepageSectionRepository::class
        );

        // Bind Advertisement Repository
        $this->app->bind(
            AdvertisementRepositoryInterface::class,
            AdvertisementRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
