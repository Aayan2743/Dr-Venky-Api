<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;

use App\Models\clinictransaction;
use App\Models\prescription;
use App\Models\appointment;
use App\Models\labreport;
use Barryvdh\DomPDF\Facade\Pdf;
class transactiondetails extends Controller
{
    //
    
    
    public function generate_precreption($id){
      
      $prescriptions1 = prescription::where('aaid', $id)->get();
$prescriptions = prescription::find($prescriptions1[0]->id);

// Get all subservices with fees and related service names
$allSubservices = $prescriptions->getAllSubservicesWithFees1();
//dd($allSubservices);
$user = prescription::with('appointment_details', 'user', 'pet', 'dr_user')
    ->where('id', $prescriptions1[0]->id)
    ->first();

// Fetch the selected services (existing service IDs)
$existingServiceIds = json_decode($user->choose_services);
$choose_services = $existingServiceIds ?: []; // Fallback to empty array if null

// Get all subservice data
$subserviceData = $allSubservices['subserviceData'];


// Add the service name to each subservice
$subserviceDataWithNames = $subserviceData->map(function ($subservice) {
    
    //dd($subservice['service_id']);
  
    // Assuming each subservice has a relationship with the service model
    $service = $subservice->service_details; // This assumes 'service' is a defined relationship method in your subservice model
    $subservice->service_name = $service ? $service->name : null; // Add service name to the subservice data
    return $subservice;
});

// Check the updated subservice data
dd($subserviceDataWithNames);
      


        
    }
    
    

    public function generate_pdf($id){

        $get_details=clinictransaction::with('user','pet')->where('id',$id)->first();

        //   dd($get_details);
        //   dd($get_details->payment_for);
        $paymentMode = $get_details->payment_mode == 1 ? 'Offline' : 'Online';
        //  dd($get_details->payment_mode);

        $data = [
            'InvoiceNo' => $get_details->id,
            'patientname' => $get_details->pet->petname,
            'state' => $get_details->user->state,
            'city' => $get_details->user->city,
            'date' => $get_details->created_at->format('M d, Y h:i a'),
            'payment' => $paymentMode,
            'payment_for' => $get_details->payment_for,
            'amount' => $get_details->amount,
        ];
    
        $pdf = Pdf::loadView('pdf.myPDF', $data)
        ->setOption('font', 'Noto Sans');
    
        return $pdf->download('example.pdf');
    }
    
    
    public function generate_pdf_all($id){



        $transactions = clinictransaction::with('user', 'pet')->where('uni_transaction_id', $id)->get();
        $totalAmount = $transactions->sum('amount');
           $pdf = Pdf::loadView('pdf.allPdf', compact('transactions','totalAmount'))
              ->setPaper('a4', 'portrait'); // Use 'landscape' for horizontal layout

      return $pdf->download('clinic_transactions.pdf');

       

// if (!$get_details) {
//     return back()->with('error', 'Transaction not found.');
// }

// $data = [
//     'InvoiceNo' => $get_details->id,
//     'patientname' => optional($get_details->pet)->petname,
//     'state' => optional($get_details->user)->state,
//     'city' => optional($get_details->user)->city,
//     'date' => $get_details->created_at->format('M d, Y h:i a'),
//     'payment' => $get_details->payment_type,
//     'payment_for' => $get_details->payment_for,
//     'amount' => $get_details->amount,
// ];

// $pdf = Pdf::loadView('pdf.myPDF', $data)->setOption('font', 'Noto Sans');

// return $pdf->download('example.pdf');
    }
    
//get_all_transaction_user


public function get_all_transaction_user(Request $request){

   
    $payment_date = $request->query('payment_date');
       

    if ($payment_date) {
       
        $get_details=clinictransaction::with('user','pet')->where('uid',Auth()->user()->id)->whereDate('created_at',$payment_date)->get();
        // $get_details=clinictransaction::get();
        
        return response()->json([
            'status'=>true,
            'data'=>$get_details
        ]);

    }


    $get_details=clinictransaction::with('user','pet')->where('uid',Auth()->user()->id)->get();
    // $get_details=clinictransaction::get();
    
    return response()->json([
        'status'=>true,
        'data'=>$get_details
    ]);


}


//get_all_transaction_user_grouping


public function get_all_transaction_user_grouping(Request $request){
    
    // Retrieve the query parameters
// $payment_date = $request->query('payment_date');
// $appointment_id = $request->query('appointment_id');
// $transaction_id = $request->query('transaction_id');
// $id = $request->query('id');

// // Start building the query
// $query = clinictransaction::with('user', 'pet')
//     ->where('uid', Auth()->user()->id);

// // Apply filters based on the provided query parameters
// if ($payment_date) {
//     $query->whereDate('created_at', $payment_date);
// }
// if ($appointment_id) {
//     $query->where('aid', $appointment_id);
// }
// if ($transaction_id) {
//     $query->where('uni_transaction_id', $transaction_id);
// }
// if ($id) {
//     $query->where('id', $id);
// }

// // Get the results
//  $get_details = $query->get()->groupBy('uni_transaction_id');
// //$get_details = $query->get()->unique('uni_transaction_id');

// // Return the response
// return response()->json([
//     'status' => true,
//     'data' => $get_details,
// ]);

$payment_date = $request->query('payment_date');
$appointment_id = $request->query('appointment_id');
$transaction_id = $request->query('transaction_id');
$id = $request->query('id');

// Start building the query
$query = clinictransaction::with('user', 'pet')
    ->where('uid', Auth()->user()->id);

// Apply filters based on the provided query parameters
if ($payment_date) {
    $query->whereDate('created_at', $payment_date);
}
if ($appointment_id) {
    $query->where('aid', $appointment_id);
}
if ($transaction_id) {
    $query->where('uni_transaction_id', $transaction_id); // Filter by `uni_transaction_id`
}
if ($id) {
    $query->where('id', $id);
}

// Fetch unique results based on `uni_transaction_id`
$get_details = $query->get()->unique('uni_transaction_id')->values()->toArray();;

// Return the response
return response()->json([
    'status' => true,
    'data' => $get_details,
]);




    
} 


public function get_all_transaction_admin_grouping(Request $request){
    

$payment_date = $request->query('payment_date');
$appointment_id = $request->query('appointment_id');
$transaction_id = $request->query('transaction_id');
$id = $request->query('id');

// Start building the query
$query = clinictransaction::with('user', 'pet');
    // ->where('uid', Auth()->user()->id);

// Apply filters based on the provided query parameters
if ($payment_date) {
    $query->whereDate('created_at', $payment_date);
}
if ($appointment_id) {
    $query->where('aid', $appointment_id);
}
if ($transaction_id) {
    $query->where('uni_transaction_id', $transaction_id); // Filter by `uni_transaction_id`
}
if ($id) {
    $query->where('id', $id);
}

// Fetch unique results based on `uni_transaction_id`
$get_details = $query->get()->unique('uni_transaction_id')->values()->toArray();;

// Return the response
return response()->json([
    'status' => true,
    'data' => $get_details,
]);




    
} 


public function get_all_transaction_user1_transaction(Request $request){
    
    // Retrieve the query parameters
$payment_date = $request->query('payment_date');
$appointment_id = $request->query('appointment_id');
$transaction_id = $request->query('transaction_id');
$id = $request->query('id');

// Start building the query
$query = clinictransaction::with('user', 'pet')
    ->where('uid', Auth()->user()->id);

// Apply filters based on the provided query parameters
if ($payment_date) {
    $query->whereDate('created_at', $payment_date);
}
if ($appointment_id) {
    $query->where('aid', $appointment_id);
}
if ($transaction_id) {
    $query->where('uni_transaction_id', $transaction_id);
}
if ($id) {
    $query->where('id', $id);
}

// Get the results
$get_details = $query->get()->groupBy('uni_transaction_id');

// Return the response
return response()->json([
    'status' => true,
    'data' => $get_details,
]);

    
} 

public function get_all_transaction_user1(Request $request)
{
    // Retrieve the query parameters
    $payment_date = $request->query('payment_date');
    $appointment_id = $request->query('appointment_id');
    $transaction_id = $request->query('transaction_id');
    $id = $request->query('id');

    // Start building the query
    $query = clinictransaction::with('user', 'pet')->where('uid', Auth()->user()->id);

    // Apply filters based on the provided query parameters
    if ($payment_date) {
        $query->whereDate('created_at', $payment_date);
    }
    if ($appointment_id) {
        $query->where('aid', $appointment_id);
    }
    if ($transaction_id) {
        $query->where('uni_transaction_id', $transaction_id);
    }
    if ($id) {
        $query->where('id', $id);
    }

    // Get the results
    $get_details = $query->get();

    // Return the response
    return response()->json([
        'status' => true,
        'data' => $get_details,
    ]);
}



    public function get_all_transaction(Request $request){


        $payment_date = $request->query('payment_date');
       

        if ($payment_date) {
           
            $get_details=clinictransaction::with('user','pet')->whereDate('created_at',$payment_date)->get();
            // $get_details=clinictransaction::get();
            
            return response()->json([
                'status'=>true,
                'data'=>$get_details
            ]);

        }









        $get_details=clinictransaction::with('user','pet')->get();
        // $get_details=clinictransaction::get();
        
        return response()->json([
            'status'=>true,
            'data'=>$get_details
        ]);


    }

}
