<?php

declare(strict_types=1);

namespace Curentis\CloudFrontSigner;

use Curentis\CloudFrontSigner\Contracts\Signer;
use Illuminate\Support\ServiceProvider;
use InvalidArgumentException;

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

                /** @var string $privateKeyPath */
                $privateKeyPath = config('cloudfront-signer.private_key_path');

                /** @var ?string $privateKeyAbsolutePath */
                $privateKeyAbsolutePath = $this->resolvePath($privateKeyPath);

                /** @var string $keyPairId */
                $keyPairId = config('cloudfront-signer.key_pair_id');

                /** @var string $region */
                $region = config('cloudfront-signer.region', 'us-east-1');

                if (is_null($privateKeyAbsolutePath)) {
                    throw new InvalidArgumentException('CloudFront private key must be a string.');
                }

                return new CloudFrontSigner(
                    keyPairId: $keyPairId,
                    privateKeyPath: $privateKeyAbsolutePath,
                    region: $region
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

    protected function resolvePath(string $path): ?string
    {
        if (empty($path)) {
            return null;
        }

        // If it's already an absolute path, don't touch it
        if (str_starts_with($path, DIRECTORY_SEPARATOR)) {
            return $path;
        }

        // Otherwise, assume it's relative to the project root
        return base_path($path);
    }
}
