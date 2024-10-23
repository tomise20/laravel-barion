### **Important:** wallet operations are in progress!

You can use the "wallet" endpoints, but development is still ongoing so bugs may occur.

## Basic usage

#### Get wallet history

```php
$wallet = new BarionWalletDto();
$wallet->setLimit(100);

$gateway = BarionGateway::createWalletGateway($wallet);

$response = $gateway->getHistory();
```

#### Get Account

```php
$wallet = new BarionWalletDto();

$gateway = BarionGateway::createWalletGateway($wallet);

$response = $gateway->getAccounts();
```

#### Download

```php
$wallet = new BarionWalletDto();
$wallet->setYear(2024);
$wallet->setMonth(12);
$wallet->setCurrency(Currency::Huf);

$gateway = BarionGateway::createWalletGateway();
$downloadUrl = $gateway->download();
```
