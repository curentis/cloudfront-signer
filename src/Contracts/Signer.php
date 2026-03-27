<?php

declare(strict_types=1);

namespace Curentis\CloudFrontSigner\Contracts;

use DateTimeInterface;
use http\Exception\RuntimeException;

/**
 * Interface Signer
 * This contract defines the necessary methods for generating secure,
 * signed access to AWS CloudFront resources.
 *
 * Implementations MUST support loading private keys in two ways:
 * 1. File-based: privateKeyPath pointing to a .pem file on disk
 * 2. Content-based: privateKeyContent containing the raw PEM key string
 *
 * This flexibility allows for both traditional file-system storage and
 * modern secret management approaches (environment variables, vaults, etc).
 */
interface Signer
{
    /**
     * Generate a signed URL using a Canned Policy.
     * This is the simplest form of signing, allowing access to a single
     * specific URL until a certain timestamp.
     *
     * The implementation will use whichever private key source is configured:
     * - If privateKeyContent is provided, it takes precedence
     * - Otherwise, privateKeyPath is used to load the key from disk
     *
     * @param  string  $url  The full CloudFront URL to sign.
     * @param  int|DateTimeInterface|null  $expires  The expiration time (Unix timestamp or DateTime).
     *                                               If null, uses the default expiration from config.
     * @return string The fully qualified signed URL.
     *
     * @throws RuntimeException If neither privateKeyPath nor privateKeyContent is available.
     * @throws RuntimeException If the private key file cannot be read (file-based approach).
     */
    public function sign(string $url, int|DateTimeInterface|null $expires = null): string;
}
