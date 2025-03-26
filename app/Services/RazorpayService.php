<?php

namespace App\Services;

use Razorpay\Api\Api;

class RazorpayService
{
    protected $api;

    public function __construct()
    {
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    public function createOrder($amount, $currency = 'INR')
    {
        return $this->api->order->create([
            'receipt' => 'order_rcptid_11',
            'amount' => $amount * 100, // Amount in paise
            'currency' => $currency,
        ]);
    }
}
 ?>