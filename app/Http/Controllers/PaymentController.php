<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\RazorpayService;

class PaymentController extends Controller
{
    
    protected $razorpayService;

    public function __construct(RazorpayService $razorpayService)
    {
        $this->razorpayService = $razorpayService;
    }

    public function createOrder(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric',
        ]);

        $order = $this->razorpayService->createOrder($request->amount);

        return response()->json([
            'order_id' => $order['id'],
            'amount' => $order['amount'],
            'currency' => $order['currency'],
            'key' => config('services.razorpay.key'),
        ]);
    }
    
    
    public function verifyPayment(Request $request)
        {
            $signature = $request->razorpay_signature;
            $paymentId = $request->razorpay_payment_id;
            $orderId = $request->razorpay_order_id;
        
            $generatedSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, config('services.razorpay.secret'));
        
            if ($generatedSignature === $signature) {
                // Payment verified
                return response()->json(['message' => 'Payment verified']);
            }
        
            return response()->json(['message' => 'Invalid payment signature'], 400);
        }

    
    
}
