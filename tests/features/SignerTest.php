<?php

declare(strict_types=1);

use Curentis\CloudFrontSigner\Facades\CloudFrontSigner;

it('generates a valid signed url string', function () {
    // We use a dummy key for testing
    $url = 'https://cdn.curentis.com/video.mp4';

    $signedUrl = CloudFrontSigner::sign($url);

    expect($signedUrl)
        ->toBeString()
        ->toContain('Signature=')
        ->toContain('Key-Pair-Id=K2JC3X6EXAMPLE');
});

it('uses custom expiration when provided', function () {
    $url     = 'https://cdn.curentis.com/video.mp4';
    $expires = now()->addMinutes(10);

    $signedUrl = CloudFrontSigner::sign($url, $expires);

    expect($signedUrl)->toContain('Expires='.$expires->getTimestamp());
});
