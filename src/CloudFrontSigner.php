<?php

declare(strict_types=1);

namespace Curentis\CloudFrontSigner;

use Aws\CloudFront\CloudFrontClient;
use Curentis\CloudFrontSigner\Contracts\Signer;
use DateTimeInterface;
use RuntimeException;

readonly class CloudFrontSigner implements Signer
{
    private CloudFrontClient $client;

    private ?string $privateKeyPath;

    private ?string $privateKeyContent;

    public function __construct(
        private string $keyPairId,
        private string $region,
        ?string $privateKeyPath = null,
        ?string $privateKeyContent = null,
    ) {
        if (empty($privateKeyPath) && empty($privateKeyContent)) {
            throw new RuntimeException('Either privateKeyPath or privateKeyContent must be provided.');
        }

        $this->privateKeyPath    = $privateKeyPath;
        $this->privateKeyContent = $privateKeyContent;

        $this->client = new CloudFrontClient([
            'region'  => $this->region,
            'version' => 'latest',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function sign(string $url, int|DateTimeInterface|null $expires = null): string
    {
        $signParams = [
            'url'         => $url,
            'expires'     => $this->resolveExpiry($expires),
            'key_pair_id' => $this->keyPairId,
        ];

        // Use private key content if available, otherwise use file path
        if ($this->privateKeyContent) {
            // Normalize escaped newlines from environment variables
            $signParams['private_key'] = $this->normalizeKeyContent($this->privateKeyContent);
        } else {
            $signParams['private_key'] = file_get_contents((string) $this->privateKeyPath);
        }

        return $this->client->getSignedUrl($signParams);
    }

    private function resolveExpiry(int|DateTimeInterface|null $expires): int
    {
        if ($expires instanceof DateTimeInterface) {
            return $expires->getTimestamp();
        }

        if (is_int($expires)) {
            return $expires;
        }

        $expires = config('cloudfront-signer.default_expiration');

        if (!is_numeric($expires)) {
            $expires = 3600; // Provide a safe default
        }

        return (int) $expires;
    }

    /**
     * Normalize key content by handling escaped newlines from environment variables.
     * Environment variables may contain escaped newlines (\n) that need to be converted
     * to actual newlines for proper PEM format parsing.
     */
    private function normalizeKeyContent(string $keyContent): string
    {
        // Replace escaped newlines with actual newlines
        return str_replace('\n', "\n", $keyContent);
    }
}
