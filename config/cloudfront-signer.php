<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | AWS Region
    |--------------------------------------------------------------------------
    |
    | While CloudFront is a global service, the AWS SDK requires a region
    | to be specified for the client. Standard practice is 'us-east-1'.
    |
    */

    'region' => env('CLOUDFRONT_REGION', 'us-east-1'),

    /*
    |--------------------------------------------------------------------------
    | CloudFront Public Key ID
    |--------------------------------------------------------------------------
    |
    | The Identifier for a public key that you have uploaded to CloudFront.
    | You can find this in the AWS Console under:
    | CloudFront > Key Management > Public Keys.
    |
    | Note: This is NOT your standard AWS IAM Access Key ID.
    |
    */

    'key_pair_id' => env('CLOUDFRONT_KEY_PAIR_ID'),

    /*
    |--------------------------------------------------------------------------
    | Private Key Path
    |--------------------------------------------------------------------------
    |
    | The full path to your CloudFront private key file (.pem). For security,
    | this file should be stored in your application's 'storage' directory
    | or another non-public location.
    |
    | Example: storage_path('certs/cloudfront-private-key.pem')
    |
    | If using CLOUDFRONT_PRIVATE_KEY_CONTENT instead, leave this empty.
    |
    */

    'private_key_path' => env('CLOUDFRONT_PRIVATE_KEY_PATH'),

    /*
    |--------------------------------------------------------------------------
    | Private Key Content
    |--------------------------------------------------------------------------
    |
    | The raw private key content (PEM format) as a string. This is preferred
    | over file paths for better security in containerized environments.
    |
    | The key should be stored as a multi-line environment variable:
    | CLOUDFRONT_PRIVATE_KEY_CONTENT="-----BEGIN PRIVATE KEY-----
    | MIIEvQIBADANBgk...
    | -----END PRIVATE KEY-----"
    |
    | Or with escaped newlines for single-line env vars:
    | CLOUDFRONT_PRIVATE_KEY_CONTENT="-----BEGIN PRIVATE KEY-----\nMIIEvQIBADANBgk...\n-----END PRIVATE KEY-----"
    |
    */

    'private_key_content' => env('CLOUDFRONT_PRIVATE_KEY_CONTENT'),

    /*
    |--------------------------------------------------------------------------
    | Default URL Expiration
    |--------------------------------------------------------------------------
    |
    | This value determines how long (in seconds) a signed URL remains valid
    | by default. After this time, CloudFront will reject any requests made
    | using the signature.
    |
    | Default: 3600 (1 Hour)
    |
    */

    'default_expiration' => (int) env('CLOUDFRONT_DEFAULT_EXPIRATION', 3600),

];
