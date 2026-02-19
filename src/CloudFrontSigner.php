<?php

declare(strict_types=1);

namespace Curentis\CloudFrontSigner;

use Aws\CloudFront\CloudFrontClient;
use Curentis\CloudFrontSigner\Contracts\Signer;
use DateTimeInterface;

readonly class CloudFrontSigner implements Signer
{
    private CloudFrontClient $client;

    public function __construct(
        private string $keyPairId,
        private string $privateKeyPath,
        private string $region,
    ) {
        $this->client = new CloudFrontClient([
            'region'  => $this->region,
            'version' => 'latest',
        ]);
    }

    public function sign(string $url, int|DateTimeInterface|null $expires = null): string
    {
        return $this->client->getSignedUrl([
            'url'         => $url,
            'expires'     => $this->resolveExpiry($expires),
            'private_key' => $this->privateKeyPath,
            'key_pair_id' => $this->keyPairId,
        ]);
    }

    private function resolveExpiry(int|DateTimeInterface|null $expires): int
    {
        if ($expires instanceof DateTimeInterface) {
            return $expires->getTimestamp();
        }

        $expires = config('cloudfront-signer.expires');

        if (!is_numeric($expires)) {
            $expires = 3600; // Provide a safe default
        }

        return (int) $expires;
    }
}
