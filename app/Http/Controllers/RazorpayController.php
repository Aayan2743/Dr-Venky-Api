<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Razorpay\Api\Api;
use App\Models\clinictransaction;
use App\Models\appointment;
use validate;

class RazorpayController extends Controller
{
    //
    
     public function createOrder(Request $request)
    {
        
         $request->validate([
        'amount' => 'required|numeric|min:1',
        'pet_id'=>'required',
       
      
                 'dateofapp'=>'required',
            // 'dateofapp' => 'required|date_format:Y-m-d|after_or_equal:today',    
      
        ]);
        
        
     
        $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

        // Create an order
        $order = $api->order->create([
            'receipt'         => uniqid(),
            'amount'          => $request->amount * 100, // amount in paise
            'currency'        => 'INR',
           
                'notes'           => [
                    'pet_id' => $request->pet_id,
                    'dateofapp' =>  $request->dateofapp,
                 ],
            
        ]);
        
        
            
        
            return response()->json([
                'order_id' => $order['id'],
                'amount'   => $order['amount'],
                'currency' => $order['currency'],
                
            'notes' => $order['notes']['pet_id'], // Optional: Display the notes field to confirm it's sent
            ]);
        
        
        
        
        
        
        
        

       
    }
    
    
    public function verifyPayment(Request $request)
{
    $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));

    try {
        $attributes = [
            'razorpay_order_id'   => $request->razorpay_order_id,
            'razorpay_payment_id' => $request->razorpay_payment_id,
            'razorpay_signature'  => $request->razorpay_signature,
        ];

        $api->utility->verifyPaymentSignature($attributes);

        // Handle successful payment
        return response()->json(['message' => 'Payment verified successfully!']);
    } catch (\Exception $e) {
        return response()->json(['error' => 'Payment verification failed!'], 400);
    }
}


public function callback(Request $request){
    
   // dd(Auth()->user()->id);    
    
     $api = new Api(env('RAZORPAY_KEY'), env('RAZORPAY_SECRET'));
     
     
    

    // Check if notes exist on the order
  //  dd($order->notes);         
     
     
     
     
     

  
    $razorpayOrderId = $request->input('razorpay_order_id');

    try {
        // Fetch payment details using Razorpay SDK
        // $payment = $api->payment->fetch($razorpayOrderId);

        // // Verify payment status
        
        //  $order = $api->order->fetch($payment->order_id);

    // Check if notes exist on the order
       $razorpayPaymentId = $request->input('razorpay_payment_id');
     $payment = $api->payment->fetch($razorpayPaymentId);

        // dd($payment);
    // Fetch the associated order using the payment's order_id
    $order = $api->order->fetch($payment->order_id);
        
    
   // dd($order->notes);  // Display notes associated with the order

        
        
       
      // dd($payment->notes['pet_id']);
      //dd($payment);
       $notes = $payment->notes;
        
        if ($payment->status === 'captured') {
            // Payment is successful, process the payment
            $paymentDetails = [
                'payment_id' => $payment->id,
                'amount' => $payment->amount / 100, // Convert paise to INR
                'currency' => $payment->currency,
                'status' => $payment->status,
            ];

            // Save details in the database
            // \App\Models\Payment::create($paymentDetails);
                
            $details="";    
            if($payment->method=='upi'){
                    $details=$payment->vpa;
            }else if($payment->method=='card'){
                    $details=$payment->card_id;
            }else if($payment->method=='netbanking'){
                    $details=$payment->bank;
            }else if($payment->method=='wallet'){
                    $details=$payment->wallet;
            }
            
              $petId = $payment->notes['pet_id'];
              $dateOfApp = $payment->notes['dateofapp'];
              
            //     dd(Auth()->user()->id);
            $create_Appointment=appointment::create([
                'user_id'=>Auth()->user()->id,
                'pet_id'=>$petId,
                'dateofapp'=>$dateOfApp,
                'amount'=> $payment->amount / 100,
                'payment'=>0,
                'app_type'=>'28',
                 'status_details'=>env('STATUS_CREATED'),
                 
                
                ]);
                
                //dd($create_Appointment->id);
                
                
                
                
            
            
            if($create_Appointment){
                 $uniqueId = time() . random_int(1000000000, 9999999999);
                $create_transaction=clinictransaction::create([
                        'payment_mode'=>'0',
                        'payment_for'=>'E-Consultancy',
                        'amount'=>$payment->amount/100,
                        'transactionid'=>$payment->id,
                        'payment_type'=>$payment->method,
                        'razorpay_paid_details'=>$details,
                        'paid_by_id'=> Auth()->user()->id,
                        'aid'=>$create_Appointment->id,
                        'uid'=>Auth()->user()->id,
                        'pid'=>$petId,
                         'uni_transaction_id'=>$uniqueId
                        
                
                
                ]);

                
                
                
                
                   return response()->json([
                        'status' => true,
                        'message' => 'Payment Successfully! Done, & Appointment Created...!',
                        'res2' => $paymentDetails,
                 ]); 
            }else {
                
                 return response()->json([
                        'status' => false,
                        'message' => 'Appointment Not Created',
                        
                 ]); 
                
            }
            
            
            // $create_transaction=clinictransaction::create([
            //     'payment_mode'=>'0',
            //     'payment_for'=>'E-Consultancy',
            //     'amount'=>$payment->amount,
            //     'razorpay_id'=>$payment->id,
            //     'razorpay_method'=>$payment->method,
            //     'razorpay_paid_details'=>$details,
            //     'paid_by_id'=> Auth()->user()->id,
                
                
            //     ]);

         
        }

        return response()->json([
            'status' => false,
            'message' => 'Payment verification failed. Payment not captured.',
        ], 400);
    } catch (\Exception $e) {
        return response()->json([
            'status' => false,
            'message' => 'Error fetching payment details: ' . $e->getMessage(),
        ], 400);
    }
}

    
    
}
