<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Contracts\InventoryRepositoryInterface;
use App\Contracts\SaleRepositoryInterface;
use App\Contracts\ReportRepositoryInterface;
use App\Contracts\ValidationRepositoryInterface;
use App\Contracts\StockOperationsInterface;
use App\Contracts\StockQueryInterface;
use App\Contracts\SaleCreationInterface;
use App\Contracts\SaleQueryInterface;
use App\Contracts\SaleProcessingInterface;
use App\Contracts\ReportGenerationInterface;
use App\Contracts\InputValidationInterface;
use App\Contracts\FilterValidationInterface;
use App\Repositories\InventoryRepository;
use App\Repositories\SaleRepository;
use App\Repositories\ReportRepository;
use App\Repositories\ValidationRepository;
use App\Repositories\CacheInventoryRepository;
use App\Repositories\FileReportRepository;
use App\Repositories\StrictValidationRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Interfaces principais
        $this->app->bind(InventoryRepositoryInterface::class, InventoryRepository::class);
        $this->app->bind(SaleRepositoryInterface::class, SaleRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, ReportRepository::class);
        $this->app->bind(ValidationRepositoryInterface::class, ValidationRepository::class);

        // Interfaces segregadas - Stock
        $this->app->bind(StockOperationsInterface::class, InventoryRepository::class);
        $this->app->bind(StockQueryInterface::class, InventoryRepository::class);

        // Interfaces segregadas - Sale
        $this->app->bind(SaleCreationInterface::class, SaleRepository::class);
        $this->app->bind(SaleQueryInterface::class, SaleRepository::class);
        $this->app->bind(SaleProcessingInterface::class, SaleRepository::class);

        // Interfaces segregadas - Report
        $this->app->bind(ReportGenerationInterface::class, ReportRepository::class);

        // Interfaces segregadas - Validation
        $this->app->bind(InputValidationInterface::class, ValidationRepository::class);
        $this->app->bind(FilterValidationInterface::class, ValidationRepository::class);

        // Implementações alternativas (LSP)
        $this->app->bind(CacheInventoryRepository::class, function ($app) {
            return new CacheInventoryRepository($app->make(InventoryRepositoryInterface::class));
        });

        $this->app->bind(FileReportRepository::class, function ($app) {
            return new FileReportRepository($app->make(ReportRepositoryInterface::class));
        });

        $this->app->bind(StrictValidationRepository::class, function ($app) {
            return new StrictValidationRepository($app->make(ValidationRepositoryInterface::class));
        });

        // Configuração condicional baseada no ambiente
        if (env('USE_CACHE_INVENTORY', false)) {
            $this->app->bind(StockOperationsInterface::class, CacheInventoryRepository::class);
            $this->app->bind(StockQueryInterface::class, CacheInventoryRepository::class);
        }

        if (env('USE_FILE_REPORTS', false)) {
            $this->app->bind(ReportGenerationInterface::class, FileReportRepository::class);
        }

        if (env('USE_STRICT_VALIDATION', false)) {
            $this->app->bind(InputValidationInterface::class, StrictValidationRepository::class);
            $this->app->bind(FilterValidationInterface::class, StrictValidationRepository::class);
        }
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
