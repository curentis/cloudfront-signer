<?php

declare(strict_types=1);

use Curentis\CloudFrontSigner\CloudFrontSigner;
use Curentis\CloudFrontSigner\Facades\CloudFrontSigner as CloudFrontSignerFacade;

it('generates a valid signed url string', function () {
    // We use a dummy key for testing
    $url = 'https://cdn.curentis.com/video.mp4';

    $signedUrl = CloudFrontSignerFacade::sign($url);

    expect($signedUrl)
        ->toBeString()
        ->toContain('Signature=')
        ->toContain('Key-Pair-Id=K2JC3X6EXAMPLE');
});

it('uses custom expiration when provided', function () {
    $url     = 'https://cdn.curentis.com/video.mp4';
    $expires = now()->addMinutes(10);

    $signedUrl = CloudFrontSignerFacade::sign($url, $expires);

    expect($signedUrl)->toContain('Expires='.(time() + $expires->getTimestamp()));
});

it('signs url using private key file path', function () {
    $keyPath = __DIR__.'/../fixtures/dummy.pem';
    $signer  = new CloudFrontSigner(
        keyPairId: 'K2JC3X6EXAMPLE',
        region: 'us-east-1',
        privateKeyPath: $keyPath,
    );

    $url       = 'https://cdn.curentis.com/video.mp4';
    $signedUrl = $signer->sign($url);

    expect($signedUrl)
        ->toBeString()
        ->toContain('Signature=')
        ->toContain('Key-Pair-Id=K2JC3X6EXAMPLE')
        ->toContain('https://cdn.curentis.com/video.mp4');
});

it('signs url using private key content from environment', function () {
    // Read the key content from the fixture file
    $keyContent = file_get_contents(__DIR__.'/../fixtures/dummy.pem');

    $signer = new CloudFrontSigner(
        keyPairId: 'K2JC3X6EXAMPLE',
        region: 'us-east-1',
        privateKeyContent: $keyContent,
    );

    $url       = 'https://cdn.curentis.com/video.mp4';
    $signedUrl = $signer->sign($url);

    expect($signedUrl)
        ->toBeString()
        ->toContain('Signature=')
        ->toContain('Key-Pair-Id=K2JC3X6EXAMPLE')
        ->toContain('https://cdn.curentis.com/video.mp4');
});

it('produces identical signatures for both key file and key content', function () {
    $keyPath    = __DIR__.'/../fixtures/dummy.pem';
    $keyContent = file_get_contents($keyPath);
    $url        = 'https://cdn.curentis.com/video.mp4';
    $timestamp  = now()->addHours(1)->getTimestamp();

    // Sign using file path
    $signerFromFile = new CloudFrontSigner(
        keyPairId: 'K2JC3X6EXAMPLE',
        region: 'us-east-1',
        privateKeyPath: $keyPath,
    );
    $signedUrlFromFile = $signerFromFile->sign($url, $timestamp);

    // Sign using content
    $signerFromContent = new CloudFrontSigner(
        keyPairId: 'K2JC3X6EXAMPLE',
        region: 'us-east-1',
        privateKeyContent: $keyContent,
    );
    $signedUrlFromContent = $signerFromContent->sign($url, $timestamp);

    // Both should produce identical signatures
    expect($signedUrlFromFile)->toEqual($signedUrlFromContent);
});

it('prefers private key content over file path when both provided', function () {
    $keyPath    = __DIR__.'/../fixtures/dummy.pem';
    $keyContent = file_get_contents($keyPath);
    $url        = 'https://cdn.curentis.com/video.mp4';
    $timestamp  = now()->addHours(1)->getTimestamp();

    // Create signer with both path and content
    $signer = new CloudFrontSigner(
        keyPairId: 'K2JC3X6EXAMPLE',
        region: 'us-east-1',
        privateKeyPath: $keyPath,
        privateKeyContent: $keyContent,
    );

    $signedUrl = $signer->sign($url, $timestamp);

    expect($signedUrl)
        ->toBeString()
        ->toContain('Signature=')
        ->toContain('Key-Pair-Id=K2JC3X6EXAMPLE');
});

it('handles key content with escaped newlines', function () {
    // Read the original key
    $keyPath            = __DIR__.'/../fixtures/dummy.pem';
    $originalKeyContent = file_get_contents($keyPath);

    // Simulate how the key comes from an environment variable with escaped newlines
    // Replace actual newlines with the escaped sequence \n
    $escapedKeyContent = str_replace("\n", '\n', $originalKeyContent);

    $signer = new CloudFrontSigner(
        keyPairId: 'K2JC3X6EXAMPLE',
        region: 'us-east-1',
        privateKeyContent: $escapedKeyContent,
    );

    $url       = 'https://cdn.curentis.com/video.mp4';
    $signedUrl = $signer->sign($url);

    expect($signedUrl)
        ->toBeString()
        ->toContain('Signature=');
});
