<?php

declare(strict_types=1);

namespace Curentis\CloudFrontSigner\Contracts;

use DateTimeInterface;

/**
 * Interface Signer
 * This contract defines the necessary methods for generating secure,
 * signed access to AWS CloudFront resources.
 */
interface Signer
{
    /**
     * Generate a signed URL using a Canned Policy.
     * This is the simplest form of signing, allowing access to a single
     * specific URL until a certain timestamp.
     *
     * @param  string  $url  The full CloudFront URL to sign.
     * @param  int|DateTimeInterface|null  $expires  The expiration time (Unix timestamp or DateTime).
     * @return string The fully qualified signed URL.
     */
    public function sign(string $url, int|DateTimeInterface|null $expires = null): string;
}
