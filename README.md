# CloudFront Signer for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/curentis/cloudfront-signer?style=flat-square&label=release&color=blue)](https://packagist.org/packages/curentis/cloudfront-signer)
[![License](https://img.shields.io/packagist/l/curentis/cloudfront-signer.svg?style=flat-square)](https://packagist.org/packages/curentis/cloudfront-signer)

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
./vendor/bin/pest
```
## Code Standards
This package adheres to the Laravel Opinionated Standards. We use Laravel Pint to maintain code style and strict typing.

To fix code style:

```bash
./vendor/bin/pint
```

## Contributing
Please see [CONTRIBUTING](https://github.com/Curentis/cloudfront-signer/blob/main/CONTRIBUTING) for details.

## License
The [Apache License 2.0](https://github.com/Curentis/cloudfront-signer/blob/main/LICENSE). Please see License File for more information.
