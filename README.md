# CloudFront Signer for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/curentis/cloudfront-signer?style=flat-square&label=release&color=blue)](https://packagist.org/packages/curentis/cloudfront-signer)
[![Tests](https://img.shields.io/github/actions/workflow/status/curentis/cloudfront-signer/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/curentis/cloudfront-signer/actions/workflows/run-tests.yml)
[![Lint](https://img.shields.io/github/actions/workflow/status/curentis/cloudfront-signer/lint.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/curentis/cloudfront-signer/actions/workflows/lint.yml)
[![Static Analysis](https://img.shields.io/github/actions/workflow/status/curentis/cloudfront-signer/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/curentis/cloudfront-signer/actions/workflows/phpstan.yml)
[![License](https://img.shields.io/github/license/curentis/cloudfront-signer?style=flat-square)](https://github.com/curentis/cloudfront-signer/blob/main/LICENSE)

A strictly-typed, modern Laravel package for generating AWS CloudFront signed URLs. Designed for high performance and seamless integration with Laravel 11 and 12+.

## Key Features

- **Strictly Typed**: Built with `declare(strict_types=1)` for maximum reliability.
- **Modern PHP**: Utilizes PHP 8.2+ features (readonly classes, constructor promotion).
- **Interface Driven**: Follows the SOLID principles, allowing you to swap implementations via the `Signer` contract.
- **Developer Experience**: Includes full IDE autocompletion, a dedicated Facade, and Pest test helpers.

## Installation

Install the package via Composer:

```bash
composer require curentis/cloudfront-signer
```

## Publish the configuration file:

```bash
php artisan vendor:publish --tag="cloudfront-signer-config"
```

## Configuration
Set your CloudFront credentials in your .env file:

```Code snippet
CLOUDFRONT_KEY_PAIR_ID=
CLOUDFRONT_PRIVATE_KEY_PATH=
CLOUDFRONT_DEFAULT_EXPIRATION=3600
```

## Usage

### Using the Facade
The quickest way to get a signed URL:

```php
use Curentis\CloudFrontSigner\Facades\CloudFrontSigner;

$url = '[https://cdn.yoursite.com/video.mp4](https://cdn.yoursite.com/video.mp4)';

// Use default expiration (1 hour)
$signedUrl = CloudFrontSigner::sign($url);

// Custom expiration using seconds or Carbon/DateTime
$signedUrl = CloudFrontSigner::sign($url, now()->addDays(7));
```
### Dependency Injection (Recommended)

Inject the Signer contract into your controllers or services for better testability:

```php
use Curentis\CloudFrontSigner\Contracts\Signer;

public function show(string $file, Signer $signer)
{
    $url = $signer->sign("[https://cdn.example.com/](https://cdn.example.com/){$file}");

    return view('video.player', compact('url'));
}
```
## Testing
We use Pest to ensure everything works perfectly.

```bash
composer test
```
## Code Standards
This package adheres to the Laravel Opinionated Standards. We use Laravel Pint to maintain code style and strict typing.

To fix code style:

```bash
composer pint --fix
```

## Static Analysis

We use [Larastan](https://github.com/larastan/larastan) (a Laravel wrapper for PHPStan) to perform static analysis on the codebase. This ensures type safety and catches potential bugs before they even reach the testing phase.

To run the analysis locally, use the following composer script:

```bash
composer phpstan
```

## License
The [Apache License 2.0](https://github.com/Curentis/cloudfront-signer/blob/main/LICENSE). Please see License File for more information.

