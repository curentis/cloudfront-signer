<?php

declare(strict_types=1);

namespace Curentis\CloudFrontSigner;

use Curentis\CloudFrontSigner\Contracts\Signer;
use Illuminate\Support\ServiceProvider;

class CloudFrontSignerServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/cloudfront-signer.php', 'cloudfront-signer');

        // Bind the Interface to the specific implementation
        $this->app->singleton(
            Signer::class,
            function ($app): CloudFrontSigner {
                return new CloudFrontSigner(
                    keyPairId: (string) config('cloudfront-signer.key_pair_id'),
                    privateKeyPath: (string) config('cloudfront-signer.private_key_path'),
                    region: (string) config('cloudfront-signer.region', 'us-east-1')
                );
            }
        );

        // Maintain the shorthand alias for the Facade
        $this->app->alias(Signer::class, 'cloudfront-signer');
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Export the config file to the main app's config directory
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/cloudfront-signer.php' => config_path('cloudfront-signer.php'),
            ], 'cloudfront-signer-config');
        }
    }
}
