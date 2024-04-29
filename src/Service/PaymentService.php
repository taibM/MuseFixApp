<?php
namespace App\Service;

use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentService
{
private $stripeSecretKey;

public function __construct(string $stripeSecretKey)
{
$this->stripeSecretKey = $stripeSecretKey;
Stripe::setApiKey($this->stripeSecretKey);
}

public function createCheckoutSession(int $amount, string $currency): string
{
$session = Session::create([
'payment_method_types' => ['card'],
'line_items' => [[
'price_data' => [
'currency' => $currency,
'product_data' => [
'name' => 'Product Name',
],
'unit_amount' => $amount,
],
'quantity' => 1,
]],
'mode' => 'payment',
'success_url' => 'https://example.com/success',
'cancel_url' => 'https://example.com/cancel',
]);

return $session->id;
}
}
