<?php

declare(strict_types=1);

namespace Curentis\CloudFrontSigner\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static string sign(string $url, int|\DateTimeInterface|null $expires = null)
 *
 * @see \Curentis\CloudFrontSigner\Contracts\Signer
 */
class CloudFrontSigner extends Facade
{
    /**
     * Get the registered name of the component.
     */
    protected static function getFacadeAccessor(): string
    {
        return 'cloudfront-signer';
    }
}
