# CloudFront Signer for Laravel

[![Latest Version on Packagist](https://img.shields.io/packagist/v/curentis/cloudfront-signer?style=flat-square&label=release&color=blue)](https://packagist.org/packages/curentis/cloudfront-signer)
[![Tests](https://img.shields.io/github/actions/workflow/status/curentis/cloudfront-signer/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/curentis/cloudfront-signer/actions/workflows/run-tests.yml)
[![Code Style](https://img.shields.io/github/actions/workflow/status/curentis/cloudfront-signer/lint.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/curentis/cloudfront-signer/actions/workflows/lint.yml)
[![Static Analysis](https://img.shields.io/github/actions/workflow/status/curentis/cloudfront-signer/phpstan.yml?branch=main&label=phpstan&style=flat-square)](https://github.com/curentis/cloudfront-signer/actions/workflows/phpstan.yml)
[![License](https://img.shields.io/github/license/curentis/cloudfront-signer?style=flat-square)](https://github.com/curentis/cloudfront-signer/blob/main/LICENSE)

A strictly-typed, modern Laravel package for generating AWS CloudFront signed URLs. Built on PHP 8.2+ with a clean interface-driven design for easy testing and extensibility.

---

## Table of Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
  - [Environment Variables](#environment-variables)
  - [Private Key Options](#private-key-options)
- [Usage](#usage)
  - [Facade](#facade)
  - [Dependency Injection](#dependency-injection)
- [Testing](#testing)
- [License](#license)

---

## Requirements

| Dependency | Version |
|------------|---------|
| PHP        | `^8.2`  |
| Laravel    | `^11.0` |
| AWS SDK    | `^3.0`  |

---

## Installation

Install via Composer:

```bash
composer require curentis/cloudfront-signer
```

The service provider and facade are auto-discovered via Laravel's package discovery. No manual registration required.

Publish the configuration file:

```bash
php artisan vendor:publish --tag="cloudfront-signer-config"
```

---

## Configuration

### Environment Variables

Add the following to your `.env` file:

```env
CLOUDFRONT_KEY_PAIR_ID=APKA...
CLOUDFRONT_REGION=us-east-1
CLOUDFRONT_DEFAULT_EXPIRATION=3600

# Choose one of the private key options below
CLOUDFRONT_PRIVATE_KEY_PATH=storage/certs/cloudfront.pem
CLOUDFRONT_PRIVATE_KEY_CONTENT=
```

| Variable                        | Required | Default      | Description                                          |
|---------------------------------|----------|--------------|------------------------------------------------------|
| `CLOUDFRONT_KEY_PAIR_ID`        | Yes      | —            | Public key ID from CloudFront > Key Management       |
| `CLOUDFRONT_REGION`             | No       | `us-east-1`  | AWS region for the SDK client                        |
| `CLOUDFRONT_DEFAULT_EXPIRATION` | No       | `3600`       | Default signed URL lifetime in seconds               |
| `CLOUDFRONT_PRIVATE_KEY_PATH`   | One of   | —            | Path to the `.pem` key file (absolute or relative)   |
| `CLOUDFRONT_PRIVATE_KEY_CONTENT`| One of   | —            | Raw PEM key string (takes precedence over path)      |

> **Note:** `CLOUDFRONT_KEY_PAIR_ID` is the public key ID found in **CloudFront > Key Management > Public Keys** — this is **not** your IAM Access Key ID.

### Private Key Options

The package supports two ways to provide your CloudFront private key:

**Option A — File path** (traditional, local environments):

Store the `.pem` file in a non-public directory such as `storage/certs/`, then set:

```env
CLOUDFRONT_PRIVATE_KEY_PATH=storage/certs/cloudfront-private-key.pem
```

Both absolute paths and paths relative to the project root are accepted.

**Option B — Inline content** (recommended for containers and secret managers):

Provide the raw PEM string directly. For single-line env vars, use escaped newlines:

```env
CLOUDFRONT_PRIVATE_KEY_CONTENT="-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgk...\n-----END PRIVATE KEY-----"
```

Multi-line format is also accepted if your environment supports it:

```env
CLOUDFRONT_PRIVATE_KEY_CONTENT="-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgk...
-----END PRIVATE KEY-----"
```

> When both options are set, `CLOUDFRONT_PRIVATE_KEY_CONTENT` takes precedence.

---

## Usage

### Facade

The `CloudFrontSigner` facade is the quickest way to generate a signed URL:

```php
use Curentis\CloudFrontSigner\Facades\CloudFrontSigner;

$url = 'https://cdn.yoursite.com/videos/intro.mp4';

// Use the default expiration defined in config
$signedUrl = CloudFrontSigner::sign($url);

// Custom expiration via Carbon or any DateTimeInterface
$signedUrl = CloudFrontSigner::sign($url, now()->addDays(7));

// Custom expiration via seconds offset
$signedUrl = CloudFrontSigner::sign($url, 86400); // expires in 24 hours
```

### Dependency Injection

Inject the `Signer` contract directly into your controllers or services. This approach is recommended as it makes the dependency explicit and keeps your code easily testable.

```php
use Curentis\CloudFrontSigner\Contracts\Signer;

class VideoController extends Controller
{
    public function __construct(private readonly Signer $signer) {}

    public function show(string $filename): \Illuminate\View\View
    {
        $signedUrl = $this->signer->sign(
            "https://cdn.example.com/videos/{$filename}",
            now()->addHours(2)
        );

        return view('video.player', compact('signedUrl'));
    }
}
```

**Mocking in tests:**

Because the package binds the `Signer` interface, you can mock it without touching any real AWS credentials:

```php
use Curentis\CloudFrontSigner\Contracts\Signer;

$this->mock(Signer::class)
    ->shouldReceive('sign')
    ->once()
    ->andReturn('https://cdn.example.com/videos/intro.mp4?Signature=...');
```

---

## Testing

Run the test suite:

```bash
composer test
```

Fix code style with Laravel Pint:

```bash
composer pint --fix
```

Run static analysis with Larastan:

```bash
composer phpstan
```

---

## License

Released under the [Apache License 2.0](https://github.com/Curentis/cloudfront-signer/blob/main/LICENSE).
