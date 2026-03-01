<?php

namespace App\Providers;

use App\Services\Salla\SallaTokenManagerService;
use App\Services\SallaAuthService;
use App\Services\Salla\SallaApiClientService;
use App\Services\SallaConnectionTester;
use App\Services\SallaDebugService;
use App\Console\Support\OrderSyncProcessorService;
use App\Console\Support\Fetchers\OrdersFetcher;
use App\Console\Support\Persisters\OrderPersister;
use App\Console\Support\Extractors\OrderProductIdExtractorService;
use App\Console\Support\Resolvers\SallaOrderStatusResolver;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }
    public function boot(): void
    {
    }
}
