<?php

namespace App\Providers;

use App\Domain\Partner\Contracts\PartnerClient;
use App\Domain\Partner\Services\FakeHarborPosClient;
use Illuminate\Support\ServiceProvider;

class PartnerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(FakeHarborPosClient::class);
        $this->app->bind(PartnerClient::class, FakeHarborPosClient::class);
    }
}
