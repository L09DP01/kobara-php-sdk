# Kobara PHP SDK

Official PHP library for integrating the **Kobara API**. This SDK makes it simple to integrate secure MonCash payments, payment links, webhooks, and manual withdrawal requests into any PHP application.

---

## Installation

Install the package via Composer:

```bash
composer require kobara/php-sdk
```

---

## Configuration

Initialize the client with your secret API key. Never expose your secret key on the frontend side.

```php
require_once 'vendor/autoload.php';

use Kobara\KobaraClient;

$kobara = new KobaraClient('your_secret_api_key');
```

To configure a different base URL (for example, for a testing sandbox):

```php
$kobara = new KobaraClient(
    'your_secret_api_key',
    'https://api.kobara.app/api/v1' // Optional
);
```

---

## Usage Examples

### 1. Payments

#### Create a Payment

Create a new payment transaction with optional metadata and a custom idempotency key to prevent double charging.

```php
try {
    $payment = $kobara->payments->create([
        'amount' => 2500,
        'currency' => 'HTG',
        'description' => 'Commande Boutique',
        'customer' => [
            'name' => 'Jean Exemple',
            'email' => 'jean@example.com',
            'phone' => '50900000000'
        ],
        'metadata' => [
            'internal_order_id' => 'ORD-89457'
        ],
        'success_url' => 'https://monsite.com/success',
        'error_url' => 'https://monsite.com/error',
        'webhook_url' => 'https://monsite.com/webhooks/kobara'
    ], 'unique-idempotency-key'); // Idempotency key is optional

    echo "Checkout URL: " . $payment['checkout_url'];
} catch (\Kobara\Exceptions\KobaraAPIException $e) {
    echo "API Error (" . $e->getStatusCode() . "): " . $e->getMessage();
} catch (\Exception $e) {
    echo "Generic Error: " . $e->getMessage();
}
```

#### Retrieve a Payment

Get the status and details of a specific payment transaction by its ID:

```php
$payment = $kobara->payments->retrieve('payment_id');
echo "Payment status: " . $payment['status'];
```

#### List Payments

List recent payment transactions with optional limit and filter by status:

```php
$response = $kobara->payments->list(10, 0, 'succeeded');
foreach ($response['data'] as $payment) {
    echo "Payment Ref: " . $payment['kobara_reference'] . "\n";
}
```

---

### 2. Payment Links

#### Create a Payment Link

Generate reusable, shareable payment links:

```php
$link = $kobara->paymentLinks->create([
    'title' => 'Ebook Tailwind CSS',
    'description' => 'Ebook premium en format PDF',
    'amount' => 500,
    'currency' => 'HTG'
]);

echo "Payment Link URL: " . $link['url'];
```

#### List Payment Links

```php
$response = $kobara->paymentLinks->list(5);
```

---

### 3. Withdrawals

#### Request a Payout

Request a manual withdrawal payout to your MonCash or Bank account:

```php
$withdrawal = $kobara->withdrawals->create([
    'amount' => 5000,
    'method' => 'moncash',
    'reference' => '50937012345'
]);

echo "Withdrawal ID: " . $withdrawal['id'];
```

#### Retrieve a Withdrawal

```php
$withdrawal = $kobara->withdrawals->retrieve('withdrawal_id');
```

---

### 4. Webhooks Verification

Securely verify that incoming webhook requests are genuinely sent by Kobara using HMAC SHA-256 validation.

#### Laravel Example

```php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kobara\KobaraClient;
use Kobara\Exceptions\KobaraSignatureVerificationException;

class KobaraWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $kobara = new KobaraClient(env('KOBARA_SECRET_KEY'));
        $signature = $request->header('Kobara-Signature');
        $payload = $request->getContent(); // Raw body string
        $secret = env('KOBARA_WEBHOOK_SECRET');

        try {
            $event = $kobara->webhooks->constructEvent($payload, $signature, $secret);
            
            if ($event['type'] === 'payment.succeeded') {
                $payment = $event['data']['payment'];
                // Process order...
            }

            return response()->json(['received' => true], 200);
        } catch (KobaraSignatureVerificationException $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}
```

#### Native PHP Example

```php
require_once 'vendor/autoload.php';

use Kobara\KobaraClient;
use Kobara\Exceptions\KobaraSignatureVerificationException;

$kobara = new KobaraClient('your_secret_api_key');
$signature = $_SERVER['HTTP_KOBARA_SIGNATURE'] ?? '';
$payload = file_get_contents('php://input'); // Raw body string
$secret = 'your_webhook_secret_key';

try {
    $event = $kobara->webhooks->constructEvent($payload, $signature, $secret);
    
    // Valid event payload
    http_response_code(200);
    echo json_encode(['received' => true]);
} catch (KobaraSignatureVerificationException $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
```

---

## Error Handling

This SDK throws custom exceptions to help you handle errors safely:

- `\Kobara\Exceptions\KobaraAPIException`: Raised for errors returned by the Kobara API endpoints (HTTP response statuses 4xx, 5xx).
- `\Kobara\Exceptions\KobaraSignatureVerificationException`: Raised by `webhooks->constructEvent()` when signature verification fails.

```php
use Kobara\Exceptions\KobaraAPIException;

try {
    $payment = $kobara->payments->retrieve('invalid-id');
} catch (KobaraAPIException $e) {
    echo "API error: Status " . $e->getStatusCode() . " - " . $e->getMessage();
} catch (\Exception $e) {
    echo "Generic error: " . $e->getMessage();
}
```

---

## License

MIT License. See [LICENSE](LICENSE) for details.
