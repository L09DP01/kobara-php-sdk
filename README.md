# Kobara PHP SDK

SDK serveur officiel pour les paiements Kobara, les retraits MonCash/NatCash et la vérification des webhooks.

```bash
composer require kobara/php-sdk
```

```php
use Kobara\KobaraClient;

$kobara = new KobaraClient($_ENV['KOBARA_SECRET_KEY']);
$payment = $kobara->payments->create([
    'amount' => 2500,
    'currency' => 'HTG',
    'provider' => 'kobara',
    'success_url' => 'https://shop.example/success',
    'cancel_url' => 'https://shop.example/cancel',
], 'payment-1001');

echo $payment['data']['checkout_url'];
```

Les paiements prennent en charge le checkout unifié, MonCash, NatCash, carte, PayPal, Apple Pay et Google Pay selon les activations du compte marchand. Les retraits publics acceptent uniquement `moncash` et `natcash`.

```php
$withdrawal = $kobara->withdrawals->create([
    'amount' => 1000,
    'method' => 'moncash',
    'account_currency' => 'HTG',
    'wallet' => '50934567890',
], 'withdrawal-1001');
```

L'URL par défaut est `https://api.kobara.app/v1`. Le SDK génère une clé d'idempotence si le second argument est omis.

Documentation: https://docs.kobara.app/docs/php-sdk
