<?php

declare(strict_types=1);

namespace Curentis\CloudFrontSigner\Tests;

use Curentis\CloudFrontSigner\CloudFrontSignerServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [CloudFrontSignerServiceProvider::class];
    }

    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('cloudfront-signer.key_pair_id', 'K2JC3X6EXAMPLE');
        $app['config']->set('cloudfront-signer.private_key_path', __DIR__.'/fixtures/dummy.pem');
        $app['config']->set('cloudfront-signer.region', 'us-east-1');
    }

    protected function defineEnvironment($app): void
    {
        // __DIR__ points to your 'tests' folder
        $app['config']->set(
            'cloudfront-signer.private_key_path',
            __DIR__.'/fixtures/dummy.pem'
        );
    }
}
