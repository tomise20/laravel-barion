# A simple Laravel package for Barion Smart Gateway

### **Connect your webshop to the Barion Smart Gateway in just 5 minutes.**

Tomise/Laravel-Barion is provides an easy way to use the Barion API with Laravel applications.Support Barion Smart Gateway operation and Barion Wallet operations.

## Installation

1. Install the package using composer:

`composer require tomise/laravel-barion`

2. Register the service provider in the `app.php` config file

```php
Tomise\Barion\Providers\BarionServiceProvider::class,
```

3. Publish config file (optional)

```php
php artisan vendor:publish --provider="Tomise\Barion\Providers\BarionServiceProvider" --tag="config"
```

## Configuration

Package has a pre-configuration, but you set some data your .env file:
Configuration details in config/barion-gateway.php

```php
#required:
BARION_POST_KEY=<your pos key>
BARION_REDIRECT_URL=<your default redirect url>
BARION_CALLBACK_URL=<your default callback url>

#optional:
# only need for wallet operations
BARION_API_KEY=<your wallet api key>
BARION_PAYEE=<payee email>

#others:
BARION_ENVIRONMENT=<default test>
BARION_GUEST_CHECKOUT=<default true>
BARION_PAYMENT_TYPE=<default Immediate>
BARION_PAYMENT_WINDOW=<default 00:30:00>
```

If your `BARION_ENVIRONMENT` variable value is "test" every request send to sandbox server.

### Documentation for Smart Gateway

[Smart Gateway documentation](docs/gateway.md)

### Documentation for Barion Wallet

[Barion Wallet documentation](docs/wallet.md)

### Examples

- [Payment example with model](examples/payment-width-models/)
- [Manual payment example](examples/manual-payment/)

## License

Laravel-Barion is open source software licensed under the [MIT License](https://opensource.org/licenses/MIT).
