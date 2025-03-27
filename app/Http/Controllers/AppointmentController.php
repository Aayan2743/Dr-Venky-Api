<?php
namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Http\Requests\appointmentRequest;
use App\Http\Requests\addAppointmentAdminRequest;
use App\Models\appointment;
use App\Models\subservice;
use App\Models\Stock;
use App\Models\User;
use App\Models\mypet;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use App\Models\clinictransaction;
use Barryvdh\DomPDF\Facade\Pdf;
use DB;
use App\Models\Order;
use App\Models\VaccinationSchedule;
// use Illuminate\Http\Exceptions\HttpResponseException;
use App\Exports\OrderProductsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Response;

class AppointmentController extends Controller
{
    //
    
        public function exportOrderProducts()
        {
            return Excel::download(new OrderProductsExport, 'order_products.xlsx');
        }
        

public function orderDetails(Request $request)
{
    
     $startDate = $request->query('startDate'); // Dynamically provided start date
    $endDate = $request->query('endDate');   // Dynamically provided end date
    
    // Ensure both dates are provided
    if (!$startDate || !$endDate) {
        return response()->json(['error' => 'Both startDate and endDate are required.'], 400);
    }

    try {
        // Parse the dates using Carbon
        $startDate = Carbon::parse($startDate)->startOfDay(); // '2024-11-13 00:00:00.000000'
        $endDate = Carbon::parse($endDate)->endOfDay();  
    } catch (\Exception $e) {
        // Return error if parsing fails
        return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
    }

    $startDateFormatted = $startDate->format('Y-m-d H:i:s.u');
    $endDateFormatted = $endDate->format('Y-m-d H:i:s.u');
    
    // Fetch orders within the date range
    $ordersQuery = Order::whereBetween('created_at', [$startDateFormatted, $endDateFormatted]);

    // Fetch orders with necessary relationships
    $orders = $ordersQuery->with(['orderProducts.product', 'address'])->get();
    
    
    
    
    
    
    
    
    
    
    
    // Initialize an empty array to hold the order details
    $orderDetailsData = [];

    // Fetch orders with related products and address
   // $orders = Order::with(['orderProducts.product', 'address','address'])->get();

    // Iterate through the orders to gather the necessary details
    foreach ($orders as $order) {
        $address = $order->address;
        $state = $address && $address->count() > 0 ? $address->first()->state : null;

        // Customer name (assuming you have a user relationship)
        $customerName = $address && $address->count() > 0 ? $address->first()->full_name : null;
        
       // dd($order);

        // Initialize an array to hold product names
        $productNames = [];

        // Iterate through the order products to fetch product details
        foreach ($order->orderProducts as $orderProduct) {

            dd($orderProduct);
            $product = $orderProduct->product;
            $productName = $product ? $product->name : 'N/A';

             
            $escapedProductName = '"' . str_replace('"', '""', $productName) . '"';
    
        
            $quantity = abs($orderProduct->quantity); // Use absolute quantity
            $discountAmount = $orderProduct->discount_amount ?? 0; // Assuming discount field exists
            $netSale = $orderProduct->net_sale ?? 0; // Assuming net sale field exists
            $taxValue = $orderProduct->tax;
            $sgst = $taxValue / 2; // Assuming SGST is half of the total tax
            $cgst = $taxValue / 2; // Assuming CGST is half of the total tax
            $igst = 0; // Assuming no IGST in this example
            $shippingCost = $order->shipping_cost ?? 0; // Assuming shipping cost field exists
            $shippingTax = $order->shipping_tax ?? 0; // Assuming shipping tax field exists
            $total = $orderProduct->total;
            
            //	5 means confirmed , 10 means delivered ; 7 on the way ;1 means pending
                $orderStatue;
          
                switch ($order->status) {
                  case 1:
                    $orderStatue="Pending";
                    break;
                  case 5:
                    $orderStatue="Confirmed";
                    break;
                  case 7:
                     $orderStatue="On The Way";
                    break;
                 case 10:
                     $orderStatue="Delivered";
                    break;  
                 case 15:
                     $orderStatue="POS Delivered";
                    break;      
                    
                  default:
                  $orderStatue="N/A";
                }
                

            // Prepare the order data for CSV
            $orderDetailsData[] = [
                'date' => $order->created_at->toDateString(),
                'order_no' => $order->order_serial_no,
                'order_status' => $orderStatue,
                'customer_name' => $customerName,
                'product_name' => $escapedProductName,  // Combine product names with commas
                'qty' => $quantity,
                'discount_amount' => $discountAmount,
                'net_sale' => $netSale,
                'sgst' => $sgst,
                'cgst' => $cgst,
                'igst' => $igst,
                'shipping_cost' => $shippingCost,
                'shipping_tax' => $shippingTax,
                'total' => $total,
            ];
        }
    }

    // Prepare CSV header
    $csvData = "Date,Order No,Order Status,Customer Name,Product Name,Qty,Discount Amount,Net Sale,SGST,CGST,IGST,Shipping Cost,Shipping Tax,Total\n";

    // Add order details to CSV content
    foreach ($orderDetailsData as $data) {
        // Combine product names in one cell, ensuring they're treated as a single value with commas
        $csvData .= "{$data['date']},{$data['order_no']},{$data['order_status']},{$data['customer_name']},{$data['product_name']},{$data['qty']},{$data['discount_amount']},{$data['net_sale']},{$data['sgst']},{$data['cgst']},{$data['igst']},{$data['shipping_cost']},{$data['shipping_tax']},{$data['total']}\n";
    }

    // Return the CSV as a downloadable response
    return response()->make($csvData, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="order_details_report.csv"',
    ]);
}

public function orderDetailsWithMargin(Request $request)
{
    // ... (same date validation and parsing logic as in the original orderDetails function) ...
    
     $startDate = $request->query('startDate'); // Dynamically provided start date
    $endDate = $request->query('endDate');   // Dynamically provided end date
    
    // Ensure both dates are provided
    if (!$startDate || !$endDate) {
        return response()->json(['error' => 'Both startDate and endDate are required.'], 400);
    }

    try {
        // Parse the dates using Carbon
        $startDate = Carbon::parse($startDate)->startOfDay(); // '2024-11-13 00:00:00.000000'
        $endDate = Carbon::parse($endDate)->endOfDay();  
    } catch (\Exception $e) {
        // Return error if parsing fails
        return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
    }

    $startDateFormatted = $startDate->format('Y-m-d H:i:s.u');
    $endDateFormatted = $endDate->format('Y-m-d H:i:s.u');
    


 $orders = Order::whereBetween('created_at', [$startDateFormatted, $endDateFormatted])
        ->with(['orderProducts.product', 'address'])->get();

    $groupedData = [];

    foreach ($orders as $order) {
        foreach ($order->orderProducts as $orderProduct) {
            $product = $orderProduct->product;
            $productName = $product ? $product->name : 'N/A';

            // Handle product names with commas within quotes
            $productName = preg_replace('/"(.*?)"/', '$1', $productName); 

            if (!isset($groupedData[$productName])) {
                $groupedData[$productName] = [
                    'product_name' => $productName,
                    'total_qty' => 0,
                    'total_cost_price' => 0,
                    'total_selling_price' => 0,
                    'total_margin' => 0,
                    'total_sales' => 0, 
                ];
            }

            $groupedData[$productName]['total_qty'] += abs($orderProduct->quantity);
            $groupedData[$productName]['total_cost_price'] += ($product ? $product->cost_price : 0) * abs($orderProduct->quantity);
            $groupedData[$productName]['total_selling_price'] += $orderProduct->selling_price * abs($orderProduct->quantity);
            $groupedData[$productName]['total_margin'] += ($orderProduct->selling_price - ($product ? $product->cost_price : 0)) * abs($orderProduct->quantity);
            $groupedData[$productName]['total_sales'] += $orderProduct->total; 
        }
    }

    $csvData = "Product Name,Total Qty,Total Cost Price,Total Selling Price,Total Margin,Total Sales\n";

    foreach ($groupedData as $productData) {
        // Enclose product names with commas in double quotes for CSV
        $productNameWithQuotes = '"' . str_replace('"', '""', $productData['product_name']) . '"'; 
        $csvData .= "{$productNameWithQuotes},{$productData['total_qty']},{$productData['total_cost_price']},{$productData['total_selling_price']},{$productData['total_margin']},{$productData['total_sales']}\n";
    }

    return response()->make($csvData, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="order_details_grouped_by_product.csv"',
    ]);

  
}

public function marginReportsCategory(Request $request)
{
    // ... (same date validation and parsing logic as in the original orderDetails function) ...
    
     $startDate = $request->query('startDate'); // Dynamically provided start date
    $endDate = $request->query('endDate');   // Dynamically provided end date
    
    // Ensure both dates are provided
    if (!$startDate || !$endDate) {
        return response()->json(['error' => 'Both startDate and endDate are required.'], 400);
    }

    try {
        // Parse the dates using Carbon
        $startDate = Carbon::parse($startDate)->startOfDay(); // '2024-11-13 00:00:00.000000'
        $endDate = Carbon::parse($endDate)->endOfDay();  
    } catch (\Exception $e) {
        // Return error if parsing fails
        return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
    }

    $startDateFormatted = $startDate->format('Y-m-d H:i:s.u');
    $endDateFormatted = $endDate->format('Y-m-d H:i:s.u');
    


 $orders = Order::whereBetween('created_at', [$startDateFormatted, $endDateFormatted])
        ->with(['orderProducts.product', 'address'])->get(); 



    $groupedData = [];

    foreach ($orders as $order) {
        //  dd($order->orderProducts);
        foreach ($order->orderProducts as $orderProduct) {
           // dd($orderProduct->product->category);
           
            $product = $orderProduct->product;
            $category = $product && $product->category ? $product->category->name : 'Uncategorized'; 
           // $category = $product && $product->category ? $product->category->name : 'Uncategorized';
            if (!isset($groupedData[$category])) {
                $groupedData[$category] = [
                    'category_name' => $category,
                    'total_qty' => 0,
                    'total_cost_price' => 0,
                    'total_selling_price' => 0,
                    'total_margin' => 0,
                    'total_sales' => 0, 
                ];
            }

            $groupedData[$category]['total_qty'] += abs($orderProduct->quantity);
            $groupedData[$category]['total_cost_price'] += ($product ? $product->cost_price : 0) * abs($orderProduct->quantity);
            $groupedData[$category]['total_selling_price'] += $orderProduct->selling_price * abs($orderProduct->quantity);
            $groupedData[$category]['total_margin'] += ($orderProduct->selling_price - ($product ? $product->cost_price : 0)) * abs($orderProduct->quantity);
            $groupedData[$category]['total_sales'] += $orderProduct->total; 
        }
    }

    $csvData = "Category Name,Total Qty,Total Cost Price,Total Selling Price,Total Margin,Total Sales\n";

    foreach ($groupedData as $categoryData) {
        $csvData .= "{$categoryData['category_name']},{$categoryData['total_qty']},{$categoryData['total_cost_price']},{$categoryData['total_selling_price']},{$categoryData['total_margin']},{$categoryData['total_sales']}\n";
    }

    return response()->make($csvData, 200, [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => 'attachment; filename="order_details_grouped_by_category.csv"',
    ]);

  
}



    
    
    public function create(Request $req){
        
    $rules = [
        'pet_owner_name' => 'required|String',
        'time' => 'required|date_format:H:i',
        'frequency' => 'required|in:daily,weekly,monthly,quarterly,half_yearly,yearly,custom',
        'mobile' => 'required|numeric|digits:10',
        'pet_name' => 'required|string',
        'reason' => 'required|string',
        'type_of_followup' => 'required|string',
        'custom_date' => 'nullable|date_format:Y-m-d',
        'app_time' => 'required|date_format:H:i',
    ];

    // Custom attribute names
    $attributes = [
        'time' => 'Reminder Time',
        'pet_owner_name' => 'Pet Owner Name',
        'frequency' => 'Reminder Frequency',
        'mobile' => 'Mobile Number',
        'pet_name' => 'Pet Name',
        'reason' => 'Reason for Reminder',
        'type_of_followup' => 'Follow-up Type',
        'custom_date' => 'Custom Date',
        'app_time' => 'Appointment Time',
    ];

    // Custom error messages
    $messages = [
       
        'frequency.in' => 'The :attribute must be one of: daily, weekly, monthly, quarterly, half-yearly, yearly, custom.',
    ];

    try {
        // Validate the request
        $validatedData = $req->validate($rules, $messages, $attributes);
            
            $create=VaccinationSchedule::create([
                'user_id'=>Auth()->user()->id,
                'time'=>$req->time,
                'frequency'=>$req->frequency,
                'mobile'=>$req->mobile,
                'pet_name'=>$req->pet_name,
                'reason'=>$req->reason,
                'type_of_followup'=>$req->type_of_followup,
                'custom_date'=>$req->custom_date,
                'pet_owner_name'=>$req->pet_owner_name,
                'app_time'=>$req->app_time,
                
                ]);
                
                if($create){
                      return response()->json([
                            'status'=>true,
                            'message' => 'Reminder created successfully!',
                            'data' => $validatedData,
                        ], 201);
                }else{
                    
                     return response()->json([
                            'status'=>false,
                            'message' => 'Reminder Not created successfully!',
                            'data' => $validatedData,
                        ], 201);
                    
                    
                }
                    
                
        

      
    } catch (\Illuminate\Validation\ValidationException $e) {
        // Handle validation errors
        return response()->json([
            'message' => 'Validation failed',
            'errors' => $e->errors(),
        ], 422);
    }
        
        
    }
    
    
    public function edit(Request $req,$id){
        
    
        try{
            
            $get_details=VaccinationSchedule::findorfail($id);
            
           
           return response()->json([
               'status'=>true,
               'data'=>$get_details
               
               ]);
           
                
            
            
        }catch(\Illuminate\Validation\ValidationException $e){
           return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);  
        }
    
        
        
    }
    
    public function update(Request $req,$id){
        
        
             
    $rules = [
         'pet_owner_name' => 'required|String',
        'time' => 'required|date_format:H:i',
        'frequency' => 'required|in:daily,weekly,monthly,quarterly,half_yearly,yearly,custom',
        'mobile' => 'required|numeric|digits:10',
        'pet_name' => 'required|string',
        'reason' => 'required|string',
        'type_of_followup' => 'required|string',
        'custom_date' => 'nullable|date_format:Y-m-d',
        'status' => 'required|in:0,1',
        'app_time' => 'required|date_format:H:i',
        
    ];

    // Custom attribute names
    $attributes = [
         'pet_owner_name' => 'required|String',
        'time' => 'Reminder Time',
        'frequency' => 'Reminder Frequency',
        'mobile' => 'Mobile Number',
        'pet_name' => 'Pet Name',
        'reason' => 'Reason for Reminder',
        'type_of_followup' => 'Follow-up Type',
        'custom_date' => 'Custom Date',
        'status' => 'Status',
        'app_time' => 'Appointment Time',
    ];

    // Custom error messages
    $messages = [
       
        'frequency.in' => 'The :attribute must be one of: daily, weekly, monthly, quarterly, half-yearly, yearly, custom.',
        'status.in' => 'The :attribute must be one of: 0 for Inactive , 1 for Active.',
    ];
        
         try{
            
             $validatedData = $req->validate($rules, $messages, $attributes);
             $check=VaccinationSchedule::where('id',$id)->count();
             if($check<=0){
                return response()->json([
               'status'=>false,
               'message'=>'Invalid id',
               ]);
                 
             }else{
                 
                 $update=VaccinationSchedule::where('id',$id)->update([
                     'time'=>$req->time,
                     'frequency'=>$req->frequency,
                     'mobile'=>$req->mobile,
                     'pet_name'=>$req->pet_name,
                     'reason'=>$req->reason,
                     'type_of_followup'=>$req->type_of_followup,
                     'custom_date'=>$req->custom_date,
                     'status'=>$req->status,
                     'pet_owner_name'=>$req->pet_owner_name,
                     'app_time'=>$req->app_time,
                      
                     ]);
                     
                    if($update==1){
                        return response()->json([
                               'status'=>true,
                               'message'=>'Reminder Updated Successfully.....!',
                          ]);
                    }else{
                         return response()->json([
                               'status'=>false,
                               'message'=>'Something went wrong please try agian later.....!',
                          ]);
                    }     
                     
                     
             }
             
        }catch(\Illuminate\Validation\ValidationException $e){
           return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);  
        }
        
    }
    
    
    public function delete($id){
         try {
        // Find the record by ID and delete it
        $get_details = VaccinationSchedule::findOrFail($id);
        $get_details->delete();

        return response()->json([
            'status' => true,
            'message' => 'Record deleted successfully.',
        ]);
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        return response()->json([
            'status' => false,
            'message' => 'Record not found.',
        ], 404);
    }
        
        
    }
    
    
    
    public function allreminders(Request $req){
          try{
              
               $type = $req->query('type');
                if ($type === 'all') {
                    $get_details = VaccinationSchedule::all();
                    
                }else if($type === 'active'){
                    
                    $get_details = VaccinationSchedule::where('status',1)->get();
                    
                }else if($type === 'in-active'){
                    $get_details = VaccinationSchedule::where('status',0)->get();
                }else{
                    $get_details = VaccinationSchedule::all();
                }
            
            
                $get_details->transform(function ($item) {
                if (isset($item->app_time)) {
                    // Remove the last three characters (":00") if present
                    if (substr($item->app_time, -3) === ':00') {
                        $item->app_time = substr($item->app_time, 0, -3);
                    }
                    
                     if (substr($item->time, -3) === ':00') {
                        $item->time = substr($item->time, 0, -3);
                    }
                }
                return $item;
                 });
                        
            
            
                //  $get_details->transform(function ($item) {
                //     if (isset($item->app_time)) {
                //         // Handle the case where only the hour is provided (e.g., 18 -> 18:00)
                //         $time_parts = explode(':', $item->app_time);
                
                //         // If there are no minutes, add ":00" to the time
                //         if (count($time_parts) == 1) {
                //             dd("fdfdf");
                //             $item->app_time .= ':00';
                //         }
                
                //         // Remove the trailing :00 if present
                //         $item->app_time = rtrim($item->app_time, ':00');
                
                //         // Ensure that the time is properly formatted to HH:MM
                //         $time_parts = explode(':', $item->app_time);
                
                //         // Ensure minutes are two digits if needed
                //         if (isset($time_parts[1])) {
                //             $time_parts[1] = str_pad($time_parts[1], 2, '0', STR_PAD_LEFT);
                //         }
                
                //         // Reassemble the time in the format HH:MM
                //         $item->app_time = implode(':', $time_parts);
                //     }
                //     return $item;
                // });
                    
                    return response()->json([
                        'status' => true,
                        'data' => $get_details
                    ]);
                   
                         
                   
                   
                    
                  
                                
            
           
          
                
            
            
        }catch(\Illuminate\Validation\ValidationException $e){
           return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);  
        }
    }
     

    
    
    public function pullOrder($id){
        
        
      $order = Order::with(['orderProducts.product'])
    ->where('order_serial_no', $id)
    ->get();

        $response = $order->map(function ($o) {
            return [
                'order_serial_no' => $o->order_serial_no,
               
                'products' => $o->orderProducts->map(function ($orderProduct) {
                    return [
                        'product_id' => $orderProduct->product->id ?? null,
                        'product_name' => $orderProduct->product->name ?? 'No name',
                        'barcode' => $orderProduct->product->barcode_id ?? 'No barcode',
                    ];
                }),
            ];
        });
        
        // Return the response in JSON format
        return response()->json($response);  
              
        
    }
    
    
    
    public function allOrder(){
        
        $orderData = Order::with(['orderProducts.product'])->get();

            $result = $orderData->map(function ($order) {
                return [
                    'order_serial_no' => $order->order_serial_no,
                  
                    'products' => $order->orderProducts->map(function ($orderProduct) {
                        return [
                            'product_id' => $orderProduct->product->id ?? null,
                            'product_name' => $orderProduct->product->name ?? 'No name',
                            'barcode' => $orderProduct->product->barcode_id ?? 'No barcode',
                        ];
                    }),
                ];
            });

// Output the result
return response()->json([
    'status' => true,
    'data' => $result,
]);
    

// dd($result);
        
    }
    
    
    public function Orders(){
        


$data = DB::table('orders')
    ->where('order_type', 15) // Filter by order_type
    // ->whereIn('created_by', [5, 39]) // Match created_by with 5 or 9
    ->get();

        
        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }
    



public function StateWiseReports(Request $request){
  

    $startDate = $request->query('startDate'); // Dynamically provided start date
    $endDate = $request->query('endDate');   // Dynamically provided end date
    
    // Ensure both dates are provided
    if (!$startDate || !$endDate) {
        return response()->json(['error' => 'Both startDate and endDate are required.'], 400);
    }

    try {
        // Parse the dates using Carbon
        $startDate = Carbon::parse($startDate)->startOfDay(); // '2024-11-13 00:00:00.000000'
        $endDate = Carbon::parse($endDate)->endOfDay();  
    } catch (\Exception $e) {
        // Return error if parsing fails
        return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
    }

    $startDateFormatted = $startDate->format('Y-m-d H:i:s.u');
    $endDateFormatted = $endDate->format('Y-m-d H:i:s.u');
    
    // Fetch orders within the date range
    $ordersQuery = Order::whereBetween('created_at', [$startDateFormatted, $endDateFormatted]);

    // Fetch orders with necessary relationships
    $orders = $ordersQuery->with(['orderProducts.product', 'address'])->get();

    // Initialize processed data
   // Initialize grouped data array
$groupedData = [];

// Process orders to group by state and calculate sales and tax-wise data
foreach ($orders as $order) {
    $address = $order->address;
    $state = $address && $address->count() > 0 ? $address->first()->state : null;

    // Default to "POS" if state is null
    $state = $state ?? "POS";

    foreach ($order->orderProducts as $orderProduct) {
        $product = $orderProduct->product;
        $quantity = abs($orderProduct->quantity); // Use absolute quantity
        $taxValue = $orderProduct->tax;
        $total = $orderProduct->total;
        $taxableAmount = $orderProduct->taxable_amount; // Assuming taxable amount is available

        // Calculate SGST, CGST, and IGST
        if ($state === 'Telangana') {
            $sgst = $taxValue / 2;
            $cgst = $taxValue / 2;
            $igst = 0;
        } else {
            $sgst = 0;
            $cgst = 0;
            $igst = $taxValue;
        }

        // Group by state and tax-wise (SGST, CGST, IGST)
        if (!isset($groupedData[$state])) {
            // Initialize data if not present for the state
            $groupedData[$state] = [
                'tax' => $taxValue,
                'qty' => 0,
                'taxable_amount' => 0,
                'igst' => 0,
                'cgst' => 0,
                'sgst' => 0,
                'total' => 0,
            ];
        }

        // Accumulate values for the state and tax
        $groupedData[$state]['qty'] += $quantity;
        $groupedData[$state]['taxable_amount'] += $taxableAmount;
        $groupedData[$state]['igst'] += $igst;
        $groupedData[$state]['cgst'] += $cgst;
        $groupedData[$state]['sgst'] += $sgst;
        $groupedData[$state]['total'] += $total;
    }
}

// Prepare the CSV data
$csvData = "State,Tax,Quantity,Taxable Amount,IGST,CGST,SGST,Total\n";

foreach ($groupedData as $state => $data) {
    $csvData .= "{$state},{$data['tax']},{$data['qty']},{$data['taxable_amount']},{$data['igst']},{$data['cgst']},{$data['sgst']},{$data['total']}\n";
}

// Return the CSV as a downloadable response
return response()->make($csvData, 200, [
    'Content-Type' => 'text/csv',
    'Content-Disposition' => 'attachment; filename="statewise_sales_report.csv"',
]);


  
    

  
  
  
}


    

    
    
    
    
    public function OrderHsnCodeFiler(Request $request){
        
        // 5 for Online 15 for POS
       $orderType = $request->query('order_type', 5); // Default order_type 5 if not provided
        $startDate = $request->query('startDate', '2025-01-01'); // Default start date
        $endDate = $request->query('endDate', '2025-01-31'); // Default end date

// Validate date format
if (!strtotime($startDate) || !strtotime($endDate)) {
    return response()->json(['error' => 'Invalid date format.'], 400);
}

// Dynamically build the query with filters
$ordersQuery = Order::where('order_type', $orderType)
                    ->whereBetween('created_at', [$startDate, $endDate]);


//dd($ordersQuery);
// Add any other dynamic filters if provided (example: filtering by product category, customer, etc.)
if ($request->has('customer_id')) {
    $ordersQuery->where('customer_id', $request->query('customer_id'));
}

// Fetch the orders
$orders = $ordersQuery->get();

// Initialize the array to store grouped HSN sales data
$hsnSalesGrouped = [];

// Process each order
foreach ($orders as $order) {
    // Retrieve and process the address
    $address = $order->address;
    $state = $address && $address->count() > 0 ? $address->first()->state : null;

    // Process each order product
    foreach ($order->orderProducts as $orderProduct) {
        // Retrieve HSN code and tax values
        
        //dd($order->created_at);
        $hsnCode = $orderProduct->product->HsnCode;
        $taxValue = $orderProduct->tax;

        // Calculate the tax percentage
        $taxPercentage = ($taxValue / ($orderProduct->total - $taxValue)) * 100;

        // Ensure the quantity is positive
        $quantity = abs($orderProduct->quantity);

        // Determine SGST, CGST, or IGST based on the state
        if ($state === 'Telangana') {
            $sgst = $taxValue / 2; // 50% for SGST
            $cgst = $taxValue / 2; // 50% for CGST
            $igst = 0; // No IGST for intra-state transactions
        } else {
            $sgst = 0;
            $cgst = 0;
            $igst = $taxValue; // Full tax value for IGST (inter-state)
        }

        // Group by HSN code and accumulate data
        if (isset($hsnSalesGrouped[$hsnCode])) {
            $hsnSalesGrouped[$hsnCode]['total_quantity'] += $quantity;
            $hsnSalesGrouped[$hsnCode]['total_amount'] += $orderProduct->total - $taxValue;
            $hsnSalesGrouped[$hsnCode]['taxable_Amount'] += $taxValue;
            $hsnSalesGrouped[$hsnCode]['state'] =$state ?? "Store Sales Telangana";
            $hsnSalesGrouped[$hsnCode]['sgst'] += $sgst;
            $hsnSalesGrouped[$hsnCode]['cgst'] += $cgst;
            $hsnSalesGrouped[$hsnCode]['igst'] += $igst;
        } else {
            // Initialize the data for new HSN code
            $hsnSalesGrouped[$hsnCode] = [
                'date' => $order->created_at,
                'total_quantity' => $quantity,
                'tax_percentage' => $taxPercentage,
                'total_amount' => $orderProduct->total - $taxValue,
                'taxable_Amount' => $taxValue,
                 'state' => $state ?? "Store Sales Telangana",
                'sgst' => $sgst,
                'cgst' => $cgst,
                'igst' => $igst,
            ];
        }
    }
}

// Prepare the CSV header dynamically based on the grouped data
$csvData = "Serial No.,Date,HSN Code,Total Quantity,Tax Percentage,Total Amount,Taxable Amount,State,SGST,CGST,IGST,Date Range\n";

// Loop through the grouped HSN data to generate CSV content
$serialNo = 1;
foreach ($hsnSalesGrouped as $hsnCode => $data) {
    $csvData .= "{$serialNo},{$data['date']},{$hsnCode},{$data['total_quantity']},{$data['tax_percentage']},{$data['total_amount']},{$data['taxable_Amount']},{$data['state']},{$data['sgst']},{$data['cgst']},{$data['igst']},{$startDate} to {$endDate}\n";
    $serialNo++;
}

// Return the CSV file as a downloadable response
return response()->make($csvData, 200, [
    'Content-Type' => 'text/csv',
    'Content-Disposition' => 'attachment; filename="hsn_sales_data.csv"',
]);

        
    }
    
    
public function ProductGrouping(Request $request){
    $startDate = $request->query('startDate'); // Dynamically provided start date
    $endDate = $request->query('endDate');   // Dynamically provided end date
    
  
    if (!$startDate || !$endDate) {
        return response()->json(['error' => 'Both startDate and endDate are required.'], 400);
    }

    try {
        // Parse the dates using Carbon
    $startDate = Carbon::parse($startDate)->startOfDay(); // '2024-11-13 00:00:00.000000'
    $endDate = Carbon::parse($endDate)->endOfDay();  
    } catch (\Exception $e) {
        // Return error if parsing fails
        return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
    }

        $startDateFormatted = $startDate->format('Y-m-d H:i:s.u');
        $endDateFormatted = $endDate->format('Y-m-d H:i:s.u');
        
       // dd($endDateFormatted);
    // Fetch orders within the date range
        $ordersQuery = Order::whereBetween('created_at', [$startDateFormatted, $endDateFormatted]);
        
            // dd($ordersQuery);

        // Fetch orders with necessary relationships
        $orders = $ordersQuery->with(['orderProducts.product', 'address'])->get();


        //dd($orders);
        \Log::info("SQL Query: " . $ordersQuery->toSql());
        \Log::info("Bindings: " . json_encode($ordersQuery->getBindings()));


        //dd($orders);
        // Initialize processed data
        $groupedProducts = [];

        // Process orders to include product details and calculations
        $totalProductsSold = 0;
        $totalSoldAmount = [];
        $totalPurchaseAmount = [];
        $totalTax = 0;


        $groupedProducts = [];

                foreach ($orderDetails as $order) {
                    $productId = $order->product_id;
                    $productName = $order->product->product_name ?? 'Unknown Product'; // Ensure product_name exists
                    $quantity = $order->quantity;
                    $total = $order->price * $quantity;
                    $taxPercentage = $order->tax_percentage;
                    $margin = $order->margin ?? 0;
                    $discountAmount = $order->discount_amount ?? 0;
                    $taxValue = ($total * $taxPercentage) / 100;
                    $totalTax = $taxValue + $taxValue;
                    $sellingPrice = $order->selling_price;
                    $hsnCode = $order->hsn_code ?? 'N/A';
                    $state = $order->state ?? 'Store Sales Telangana';
                    $orderType = $order->order_type ?? 'Regular';
                    
                    // GST calculations
                    $sgstPercentage = $order->sgst_percentage ?? 0;
                    $cgstPercentage = $order->cgst_percentage ?? 0;
                    $igstPercentage = $order->igst_percentage ?? 0;
                    $sgst = ($total * $sgstPercentage) / 100;
                    $cgst = ($total * $cgstPercentage) / 100;
                    $igst = ($total * $igstPercentage) / 100;
                    
                    // Avoid CSV issues by escaping double quotes in product names
                    $escapedProductName = '"' . str_replace('"', '""', $productName) . '"';
                    
                    // Unique grouping key (Ensures different names don't get merged)
                    $groupKey = $productId . '-' . $escapedProductName;
                    
                    if (isset($groupedProducts[$groupKey])) {
                        // Update existing entry for the product
                        $groupedProducts[$groupKey]['quantity'] += $quantity;
                        $groupedProducts[$groupKey]['total_amount'] += $total;
                        $groupedProducts[$groupKey]['taxable_amount'] += $taxValue;
                        $groupedProducts[$groupKey]['taxable_amounts'] += $totalTax;
                        $groupedProducts[$groupKey]['margin'] += ($margin * $quantity);
                        $groupedProducts[$groupKey]['discount_amount'] += $discountAmount;
                    } else {
                        // Initialize new product entry
                        $groupedProducts[$groupKey] = [
                            'product_name' => $escapedProductName,
                            'quantity' => $quantity,
                            'total_margin' => $margin,
                            'total_amount' => $total,
                            'taxable_amount' => $taxValue + $taxValue,
                            'taxable_amounts' => $quantity,
                            'state' => $state,
                            'sgst' => $sgst,
                            'cgst' => $cgst,
                            'igst' => $igst,
                            'sgstP' => $sgstPercentage,
                            'cgstP' => $cgstPercentage,
                            'igstP' => $igstPercentage,
                            'tax_percentage' => $taxPercentage,
                            'selling_price' => $sellingPrice,
                            'hsn_code' => $hsnCode,
                            'avg_rate' => round($total / $quantity, 2), // Average rate per unit
                            'avg_rate_without_tax' => round(($total - $totalTax) / $quantity, 2),
                            'discount_amount' => $discountAmount,
                            'margin' => $margin,
                            'order_type' => $orderType,
                        ];
                    }
                }

                // Convert the grouped data into a final array
                $finalProductList = array_values($groupedProducts);

                // Return or display as needed
                return $finalProductList;




}


public function ProductWiseReport_bkp(Request $request){
       
    $startDate = $request->query('startDate'); // Dynamically provided start date
    $endDate = $request->query('endDate');   // Dynamically provided end date
    
  
    if (!$startDate || !$endDate) {
        return response()->json(['error' => 'Both startDate and endDate are required.'], 400);
    }

    try {
        // Parse the dates using Carbon
    $startDate = Carbon::parse($startDate)->startOfDay(); // '2024-11-13 00:00:00.000000'
    $endDate = Carbon::parse($endDate)->endOfDay();  
    } catch (\Exception $e) {
        // Return error if parsing fails
        return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
    }

        $startDateFormatted = $startDate->format('Y-m-d H:i:s.u');
        $endDateFormatted = $endDate->format('Y-m-d H:i:s.u');
        
       // dd($endDateFormatted);
    // Fetch orders within the date range
    $ordersQuery = Order::whereBetween('created_at', [$startDateFormatted, $endDateFormatted]);
  
       // dd($ordersQuery);


        $orders = $ordersQuery->with(['orderProducts.product', 'address'])->get();


 

            //dd($orders);
            // Initialize processed data
            $groupedProducts = [];

            // Process orders to include product details and calculations
            $totalProductsSold = 0;
            $totalSoldAmount = [];
            $totalPurchaseAmount = [];
            $totalTax = 0;

    
        foreach ($orders as $order) {
       
  
 
    $address = $order->address;
   
   //dd($order->address);
    $state = $address && $address->count() > 0 ? $address->first()->state : null;
    $orderType = $order->order_type;  // Get the order type
 
 
// dd($order->orderProducts);

    foreach ($order->orderProducts as $key=> $orderProduct) {
    
  
      $productId = $orderProduct->product_id;
      
      
    
        $price = $orderProduct->price; // Assuming each product has a price field
        
          $purchasePrice = $orderProduct->product->buying_price; 
         
        $quantity =  abs($orderProduct->quantity);

        if (!isset($totalSoldAmount[$productId])) {
            $totalSoldAmount[$productId] = 0;
        }

        $totalSoldAmount[$productId] += ($price * $quantity);
        
    
    
       if (!isset($totalPurchaseAmount[$productId])) {
            $totalPurchaseAmount[$productId] = 0;
        }
        $totalPurchaseAmount[$productId] += ($purchasePrice * $quantity);
        
    
        $product = $orderProduct->product;
       

        $productId = $product->id;
        $productName = $product->name ?? 'N/A';
        $quantity = abs($orderProduct->quantity);
        // $taxValue = $orderProduct->tax;
        $taxRate = optional($orderProduct->product->productTaxes->first())->tax->tax_rate ?? 0;
        
        // $newTaxValue=$product->selling_price/(1+($taxRate/100));
        $newTaxPercentage = round((1 + ($taxRate / 100)), 2);
        $newMRPWithOutTax = round($product->selling_price / (1 + ($taxRate / 100)), 2);
        $taxValue= $product->selling_price-$newMRPWithOutTax;
        
        $totalTax += $taxValue;
       //dd($taxValue);
      
        
        $total = $orderProduct->total-$orderProduct->tax;
       
        $escapedProductName = '"' . str_replace('"', '""', $productName) . '"';

        // Calculate margin, selling price, and discount amount
        $margin =  ($product->selling_price - $product->buying_price) ;
        $Total_Avg_margin =  ($product->selling_price - $product->buying_price) ;
        
        // $margin = $product->selling_price;
        $sellingPrice = $product->selling_price ?? 0;
        $hsnCode = $product->HsnCode ?? 'N/A';
        $discountAmount = $orderProduct->discount;
        
      
        $avgRate = $product->selling_price;
      
        $avgRateWithoutTax =$newMRPWithOutTax;

 
        
        
        if ($state === 'Telangana' || $orderType == 15) {
            
           // dd( $taxValue / 2);
           // $sgsts = $taxValue / 2;
            $sgst = $taxValue / 2;
            $cgst = $taxValue / 2;
            $igst = 0;
            // $taxPercentage = ($taxValue / ($total - $taxValue)) * 100;
            $taxPercentage =$taxRate;
            $sgstPercentage = $taxPercentage / 2;
            $cgstPercentage = $taxPercentage / 2;
            $igstPercentage = 0;
        }
       
        
        else {
            
            
            // $taxPercentage = ($taxValue / ($total - $taxValue)) * 100;
            $taxPercentage = $taxRate;
            $sgst = 0;
            $cgst = 0;
            $igst = $taxValue;
            $sgstPercentage = 0;
            $cgstPercentage = 0;
            $igstPercentage = $taxPercentage;
        }
        
     

        // Group the data by product ID
        if (isset($groupedProducts[$productId])) {
            // Update existing entry for the product
            $groupedProducts[$productId]['quantity'] += $quantity;
            $groupedProducts[$productId]['total_amount'] += $total;
            $groupedProducts[$productId]['taxable_amount'] +=$taxValue;
            $groupedProducts[$productId]['taxable_amounts'] +=$totalTax;
            
         
        
            $groupedProducts[$productId]['margin'] += ($margin * $quantity);  // Accumulate total margin
            $groupedProducts[$productId]['discount_amount'] += $discountAmount;
        } else {
            // Initialize the data for new product
            $groupedProducts[$productId] = [
               
                'product_name' => $escapedProductName,
                'quantity' => $quantity,
                // 'total_margin' => ($margin * $quantity),  // Accumulate total margin
                'total_margin' => ($margin ),  // Accumulate total margin
                'total_amount' => $total,
                'taxable_amount' => $taxValue+$taxValue,
                'taxable_amounts' => $quantity,
                'state' => $state ?? 'Store Sales Telangana',
                'sgst' => $sgst,
                'cgst' => $cgst,
                'igst' => $igst,
                
                'sgstP' => $sgstPercentage,
                'cgstP' => $cgstPercentage,
                'igstP' => $igstPercentage,
                
                'tax_percentage' => $taxPercentage,
                'selling_price' => $sellingPrice,
                'hsn_code' => $hsnCode,
                'avg_rate' => round($avgRate, 2),
                'avg_rate_without_tax' => round($avgRateWithoutTax, 2),
                
                
                'discount_amount' => $discountAmount,
                'margin' => $margin,
                'order_type' => $orderType,  // Include order type here
            ];
                }
            }
        }




            foreach ($groupedProducts as $productId => &$data) {
                
            
            
                $totalSold = $totalSoldAmount[$productId] ?? 0;
            
            
                $totalPurchase = $totalPurchaseAmount[$productId] ?? 0;
                
                $data['total_avg_margin'] = $totalSold - $totalPurchase; 
                $data['total_purchase'] = $totalPurchase; 
                $data['total_amount_without_tax'] = $avgRateWithoutTax * $data['quantity']; 
                
                
                // Calculate total average margin by dividing total margin by quantity
                if ($data['quantity'] > 0) {
                    $data['avg_margin'] = round($data['total_margin'] / $data['quantity'], 2);
                } else {
                    $data['avg_margin'] = 0; // Default to 0 if no quantity
                }
            }


            //dd(taxable_amounts);


            // Prepare the CSV header, including 'Order Type'
            $csvData = "Serial No.,Product Name,Quantity,Avg Margin,Total Avg Margin,Selling Price,HSN Code,SGST%,CGST%,IGST%,Discount Amount, MRP Rate,MRP Rate Without Tax,SGST,CGST,IGST,Total Amount,Taxable Amount,State,Tax Percentage,Order Type,Date Range\n";

            // Generate CSV rows, including 'Order Type'
            $serialNo = 1;
            foreach ($groupedProducts as $productId => $data) {
                $csvData .= "{$serialNo},{$data['product_name']},{$data['quantity']},{$data['avg_margin']},{$data['total_avg_margin']},{$data['selling_price']},{$data['hsn_code']},{$data['sgstP']},{$data['cgstP']},{$data['igstP']},{$data['discount_amount']},{$data['avg_rate']},{$data['avg_rate_without_tax']},{$data['sgst']},{$data['cgst']},{$data['igst']},{$data['total_amount']},{$data['total_amount_without_tax']},{$data['state']},{$data['tax_percentage']},{$data['order_type']},{$startDate} to {$endDate}\n";
                $serialNo++;
            }

            // Return the CSV file as a downloadable response
            return response()->make($csvData, 200, [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="product_sales_data.csv"',
            ]);



    }




 public function ProductWiseReport(Request $request){

    $startDate = $request->query('startDate'); // Dynamically provided start date
    $endDate = $request->query('endDate');   // Dynamically provided end date
    
  
    if (!$startDate || !$endDate) {
        return response()->json(['error' => 'Both startDate and endDate are required.'], 400);
    }

    try {
        // Parse the dates using Carbon
    $startDate = Carbon::parse($startDate)->startOfDay(); // '2024-11-13 00:00:00.000000'
    $endDate = Carbon::parse($endDate)->endOfDay();  
    } catch (\Exception $e) {
        // Return error if parsing fails
        return response()->json(['error' => 'Invalid date format. Use YYYY-MM-DD.'], 400);
    }

        $startDateFormatted = $startDate->format('Y-m-d H:i:s.u');
        $endDateFormatted = $endDate->format('Y-m-d H:i:s.u');
       
    $ordersQuery = Order::whereBetween('created_at', [$startDateFormatted, $endDateFormatted])
    ->with(['orderProducts.product','address'])
    ->get();

    

// Product-wise data prepare cheyyali
$productWiseReport = [];
$total_pos_orders = 0; 
$total_non_local_orders = 0; 
        $total_tel_orders = 0;
        $total_CGSTValue = 0;
        $total_SGSTValue = 0;
        $total_IGSTValue = 0;

        $IGST_Amount = 0;
        $IGST_Percentage = 0;
        $CGST_Amount = 0;
        $CGST_Percentage = 0;
        $SGST_Amount = 0;
        $SGST_Percentage = 0;

foreach ($ordersQuery as $order) {
    
    

    $state = optional($order->address->first())->state ?? 'N/A';
    
    $order_type=$order->order_type;  // 5 for Online 15 for POS

   

    foreach ($order->orderProducts as $orderProduct) {
        //  dd($orderProduct->discount);



        $taxPercentage = optional($orderProduct->product->productTaxes->first())->tax->tax_rate ?? 0;
        $taxAmount =$orderProduct->tax;
      
        if ($state == "Telangana") {
            $total_tel_orders++;
        }

        if ($state != "Telangana") {
            $total_non_local_orders++;
        }

        //total_non_local_orders

        if ($order_type == 15) {
            $total_pos_orders++;
        }
      

        if (($order_type == 5 || $order_type == 15) && $state == "Telangana") {
          
            $CGST_Amount = $taxAmount / 2;
            $CGST_Percentage = $taxPercentage / 2;
            $SGST_Amount = $taxAmount / 2;
            $SGST_Percentage = $taxPercentage / 2;
        
            $total_CGSTValue += $CGST_Amount; // ✅ Accumulate CGST
            $total_SGSTValue += $SGST_Amount; // ✅ Accumulate SGST

        } else {

            $IGST_Amount = $taxAmount;
            $IGST_Percentage = $taxPercentage;
            $total_IGSTValue += $IGST_Amount; // ✅ Accumulate IGST



           
        }




     

        $purchase_cost = $orderProduct->product->buying_price;
        $selling_cost = $orderProduct->product->selling_price; // MRP
        $total_purchased_amount=abs($orderProduct->quantity)*$purchase_cost;
        $hsnCode=$orderProduct->product->HsnCode;
        $discount_amount=$orderProduct->discount;
        $selling_cost_without_tax=$selling_cost-$taxAmount;
     
        $avg_margin_cost = $selling_cost-$purchase_cost;
        $productId = $orderProduct->product->id;
        $productName = $orderProduct->product->name;
        $quantity = abs($orderProduct->quantity);
        $price = $orderProduct->price;
        $total = $quantity * $price;
        $total_avg_margin= $total-$total_purchased_amount;
      

      

        if (!isset($productWiseReport[$productId])) {
            $productWiseReport[$productId] = [
                'product_name' => $productName,
                'total_orders' => 0,
                'total_telangana_orders' => 0, 
                'total_non_local_orders' => 0, 
                'total_pos_orders' => 0,
                'total_quantity' => 0,
                'total_selled_amount' => 0,
                'total_selled_amount_without_tax' => 0,
                'purchase_cost' => 0,
                'total_purchased_amount' => 0,
                'selling_cost' => 0,
                'selling_cost_without_tax' => 0,
                'avg_margin_cost' => 0,
                'total_avg_margin' => 0,
               
                
                'hsnCode' => 0,
                // 'IGST_Amount' => 0,
                'IGST_Percentage' => 0,
                'total_IGSTValue' => 0,

                'CGST_Percentage' => 0,
                'CGST_Amount' => 0,
                'total_CGSTValue' => 0,

                'SGST_Percentage' => 0,
                'SGST_Amount' => 0,
                'total_SGSTValue' => 0,

                'discount_amount' => 0,
              
                'taxPercentage' => 0,
                'state' => 0,
                'order_type' => 0,
            ];
        }

        
      
       

        $productWiseReport[$productId]['total_orders'] += 1;
        $productWiseReport[$productId]['total_quantity'] += $quantity;
        $productWiseReport[$productId]['total_selled_amount'] += $total;
        $productWiseReport[$productId]['total_selled_amount_without_tax'] += $selling_cost_without_tax;
        $productWiseReport[$productId]['total_avg_margin'] += $total_avg_margin;
        $productWiseReport[$productId]['purchase_cost'] = $purchase_cost;
        $productWiseReport[$productId]['selling_cost'] = $selling_cost;
        $productWiseReport[$productId]['avg_margin_cost'] = $avg_margin_cost;
      
        $productWiseReport[$productId]['hsnCode'] = $hsnCode;
        // $productWiseReport[$productId]['IGST_Amount'] += $IGST_Amount;
        $productWiseReport[$productId]['IGST_Percentage'] = $IGST_Percentage;
        $productWiseReport[$productId]['total_IGSTValue'] += $IGST_Amount;
        $productWiseReport[$productId]['total_purchased_amount'] += $total_purchased_amount;

        $productWiseReport[$productId]['discount_amount'] += $discount_amount;

        $productWiseReport[$productId]['CGST_Amount'] = $CGST_Amount;
        $productWiseReport[$productId]['CGST_Percentage'] = $CGST_Percentage;
        $productWiseReport[$productId]['total_CGSTValue'] = $total_CGSTValue;
        // $productWiseReport[$productId]['total_CGSTValue'] += $CGST_Amount;

        $productWiseReport[$productId]['SGST_Amount'] = $SGST_Amount;
        $productWiseReport[$productId]['SGST_Percentage'] = $SGST_Percentage;

        $productWiseReport[$productId]['total_SGSTValue'] = $total_SGSTValue;
        $productWiseReport[$productId]['selling_cost_without_tax'] = $selling_cost_without_tax;
        $productWiseReport[$productId]['taxPercentage'] = $taxPercentage;
        $productWiseReport[$productId]['state'] = $state;
        $productWiseReport[$productId]['order_type'] = $order_type;

        // if ($state == "Telangana") {
        //     $productWiseReport[$productId]['total_telangana_orders'] += 1;
        // }

        // if ($state != "Telangana") {
        //     $productWiseReport[$productId]['total_non_local_orders'] = ($productWiseReport[$productId]['total_non_local_orders']+1) - $productWiseReport[$productId]['total_pos_orders'] ;
        // }

        // if ($order_type == 15) {
        //     $productWiseReport[$productId]['total_pos_orders'] += 1;
        // }


        if (!isset($productWiseReport[$productId]['total_non_local_orders'])) {
            $productWiseReport[$productId]['total_non_local_orders'] = 0;
        }
        
        if ($state == "Telangana") {
            $productWiseReport[$productId]['total_telangana_orders'] += 1;
        } else {
            $productWiseReport[$productId]['total_non_local_orders'] += 1;
        }
        
        if ($order_type == 15) {
            $productWiseReport[$productId]['total_pos_orders'] += 1;
        }


        //total_non_local_orders

    }
 
}


dd($productWiseReport);

  // Prepare the CSV header, including 'Order Type'
  $csvData = "Serial No.,Product Name,Quantity,Avg Margin,Total Avg Margin,Selling Price,HSN Code,SGST%,CGST%,IGST%,Discount Amount,MRP Rate,MRP Rate Without Tax,SGST,CGST,IGST,Taxable Amount,Total Amount\n";

  $serialNo = 1;
  foreach ($productWiseReport as $productId => $data) {
      $csvData .= "{$serialNo},{$data['product_name']},{$data['total_quantity']},{$data['avg_margin_cost']},{$data['total_avg_margin']},{$data['selling_cost']},{$data['hsnCode']},{$data['SGST_Percentage']},{$data['CGST_Percentage']},{$data['IGST_Percentage']},{$data['discount_amount']},{$data['selling_cost']},{$data['selling_cost_without_tax']},{$data['total_SGSTValue']},{$data['total_CGSTValue']},{$data['total_IGSTValue']},{$data['total_selled_amount_without_tax']},{$data['total_selled_amount']}\n";
      $serialNo++;
  }

 

  // Return the CSV file as a downloadable response
  return response()->make($csvData, 200, [
      'Content-Type' => 'text/csv',
      'Content-Disposition' => 'attachment; filename="product_sales_data.csv"',
  ]);



}
    
    
 public function OrdersHsnCode(){
         
// Retrieve all orders with orderProducts and products

// for Online
$orders = Order::where('order_type', 5)->get(); // Retrieve all orders

// Initialize an empty array to store the quantities grouped by HSN code and their tax percentages
$hsnSalesGrouped = [];

// Loop through each order and its order products
foreach ($orders as $order) {
    // Retrieve the address (ensure this is a valid relationship)
    $address = $order->address;  // Assuming address is a collection of addresses

    // Check if address exists and retrieve the first address if it's a collection
    if ($address && $address->count() > 0) {
        // Get the first address in the collection
        $address = $address->first();
        
        // Check if the state exists on the first address
        if ($address->state) {
            $state = $address->state;  // If the state exists, store it
        } else {
            $state = null;  // If the state is missing, set it to null
        }
    } else {
        $state = null;  // If there are no addresses, set the state to null
    }

    foreach ($order->orderProducts as $orderProduct) {
        // Get the HSN code of the product associated with the orderProduct
        $hsnCode = $orderProduct->product->HsnCode;

        // Assuming tax percentage is a property of the product
        $taxValue = $orderProduct->tax; // Replace this with the actual tax value property

       // dd($taxValue);
        // Calculate the tax percentage
        $taxPercentage = ($taxValue / ($orderProduct->total - $taxValue)) * 100;

        // Ensure that the quantity is always positive
        $quantity = abs($orderProduct->quantity); // Use absolute value to avoid negative quantities

        // Determine the type of tax (SGST, CGST, or IGST) based on the state
        if ($state === 'Telangana') {
            // For intra-state, calculate SGST and CGST (50% each)
            $sgst = $taxValue / 2; // 50% of the tax value for SGST
            $cgst = $taxValue / 2; // 50% of the tax value for CGST
            $igst = 0; // No IGST for intra-state transactions
        } else {
            // For inter-state, calculate IGST
            $sgst = 0;
            $cgst = 0;
            $igst = $taxValue; // Full tax value for IGST
        }


//dd($orderProduct->total);
        // If the HSN code exists in the grouped array, add the quantity to the existing sum
        if (isset($hsnSalesGrouped[$hsnCode])) {
            $hsnSalesGrouped[$hsnCode]['total_quantity'] += $quantity;
            $hsnSalesGrouped[$hsnCode]['total_amount'] += $orderProduct->total - $taxValue; // Sum the total amounts
            $hsnSalesGrouped[$hsnCode]['taxable_Amount'] += $taxValue; // Sum the taxable amounts
            $hsnSalesGrouped[$hsnCode]['state'] = $state; // Store the state for the HSN code
            $hsnSalesGrouped[$hsnCode]['sgst'] += $sgst; // Store the SGST for the HSN code
            $hsnSalesGrouped[$hsnCode]['cgst'] += $cgst; // Store the CGST for the HSN code
            $hsnSalesGrouped[$hsnCode]['igst'] += $igst; // Store the IGST for the HSN code
        } else {
            // If the HSN code doesn't exist, initialize the data with the current product's quantity and tax percentage
            $hsnSalesGrouped[$hsnCode] = [
                'total_quantity' => $quantity,
                'tax_percentage' => $taxPercentage, // Store the tax percentage for the HSN code
                'total_amount' => $orderProduct->total - $taxValue, // Store the taxable amount
                'taxable_Amount' => $taxValue, // Store the tax value for the HSN code
                'state' => $state, // Store the state for the HSN code
                'sgst' => $sgst, // Store the SGST for the HSN code
                'cgst' => $cgst, // Store the CGST for the HSN code
                'igst' => $igst, // Store the IGST for the HSN code
            ];
        }
    }
}

// Dump the grouped quantities and tax percentage by HSN code
dd($hsnSalesGrouped);

// Dump the grouped quantities and tax percentage by HSN code









 

// $orderProducts = Order::with('orderDetails.product') // Eager load orderDetails and the associated product
//     ->get(); // Fetch orders

// $hsnSales = $orderProducts->flatMap(function ($order) {
//     return $order->orderDetails->map(function ($orderDetail) {
//         return [
//             'hsncode' => $orderDetail->product->HsnCode, // Accessing HSN Code from the product
//             'quantity' => $orderDetail->quantity // Accessing the ordered quantity
//         ];
//     });
// })->groupBy('hsncode')->map(function ($groupedOrderDetails, $hsncode) {
//     // Sum the quantities for each HSN Code (total ordered quantity)
//     $totalQuantity = $groupedOrderDetails->sum('quantity');

//     return [
//         'hsncode' => $hsncode, // HSN Code for the group
//         'no_of_qty' => $totalQuantity, // Total ordered quantity for this HSN Code
//     ];
// });

// return response()->json([
//     'status' => true,
//     'data' => $hsnSales, // Grouped data with ordered quantity for each HSN Code
// ]);


// $orderProducts = Stock::with('product', 'order.address') // Eager load both product, order, and address
//     ->where('model_type', 'App\Models\Order')
//     ->get(); // Get all related products and orders


// //dd($orderProducts);
// // Group products by SKU (or HSN) and calculate total quantity, tax, and other values


// $hsnSales = $orderProducts->groupBy(function ($stock) {
//     return $stock->product->HsnCode; // Group by SKU (or HSN code if needed)
// })->map(function ($groupedStocks, $sku) {
//     // Fetch HSN code from the product of the first stock in the group
//     $hsnCode = $groupedStocks->first()->product->HsnCode ?? null; // Assuming the `hsn_code` column exists in the `products` table
    
//     //dd($hsnCode);
//     // Sum of quantity for the SKU
    
//   // dd($groupedStocks);
//   // $totalQuantity = $groupedStocks->sum('quantity'); // Sum of quantity for the group
    
//      $totalQuantity = $groupedStocks->sum(function ($stock) {
//         return $stock->product->HsnCode;
//     });

    
    
    
//     // Calculate total selling price for each group (price * quantity)
//     $totalSellingPrice = $groupedStocks->sum(function ($stock) {
//         return $stock->product->selling_price * $stock->quantity;
//     });

//     // Initialize CGST, SGST, IGST
//     $cgst = 0;
//     $sgst = 0;
//     $igst = 0;

//     // Get the first order
//     $firstOrder = $groupedStocks->first()->order ?? null; // Ensure the order relationship is not null
//     $orderAddress = $firstOrder ? $firstOrder->address->first() : null; // Safely get the address if the order exists
    
//     if ($orderAddress) {
//         // Check the order address state
//         if ($orderAddress->state == 'Telangana') {
//             // Calculate CGST and SGST for Telangana (9% each)
//             $cgst = $totalSellingPrice * 0.09; // CGST value (9% of total selling price)
//             $sgst = $totalSellingPrice * 0.09; // SGST value (9% of total selling price)
//         } else {
//             // Calculate IGST for other states (18%)
//             $igst = $totalSellingPrice * 0.18; // IGST value (18% of total selling price)
//         }
//     }

//     // Calculate total tax amount (CGST + SGST or IGST)
//     $totalTaxAmount = $cgst + $sgst + $igst;

//     return [
//         'sku' => $sku, // SKU as the identifier (or HSN code)
//         'hsn_code' => $hsnCode, // HSN code of the product
//         'total_quantity' => $totalQuantity,
//         'cgst' => round($cgst, 2), // CGST amount
//         'sgst' => round($sgst, 2), // SGST amount
//         'igst' => round($igst, 2), // IGST amount
//         'total_tax_amount' => round($totalTaxAmount, 2), // Total tax amount for this SKU
//         'total_selling_price' => round($totalSellingPrice, 2) // Total selling price for this SKU
//     ];
// });

// return response()->json([
//     'status' => true,
//     'data' => $hsnSales, // Return the grouped SKU data with quantity count, tax details, and total amount
// ]);







 



    }
    
    
       public function Orders_print(){
 
 $orderProducts = Stock::with('product', 'order.address') // Eager load both product, order, and address
    ->where('model_type', 'App\Models\Order')
    ->get(); // Get all related products and orders

return response()->json([
    'status' => true,
    'data' => $orderProducts->map(function ($stock) {
        // Lazy load the product name and price
        $productName = $stock->product ? $stock->product->name : null;
        $productPrice = $stock->product ? $stock->product->buying_price : null;
        $margin = $stock->product->selling_price - $stock->product->buying_price;
        
        // Eager load the order details
        $order = $stock->order; // Access the related order
        
        // Map order_type to its label
        $orderTypeLabel = null;
        if ($order) {
            switch ($order->order_type) {
                case 5:
                    $orderTypeLabel = 'Online';
                    break;
                case 15:
                    $orderTypeLabel = 'POS';
                    break;
                default:
                    $orderTypeLabel = 'Unknown'; // Handle other cases or set a default
                    break;
            }
        }

        // Get order address details
        $orderAddress = $order ? $order->address->first() : null; // Assuming there's a one-to-many relation for address
        
        // Default values for CGST, SGST, and IGST
        $cgst = 0;
        $sgst = 0;
        $igst = 0;
        $totalAmount = $stock->total; // Start with the final price

        // Check if the order is from Telangana or other states
        if ($orderAddress) {
            if ($orderAddress->state == 'Telangana') {
                // CGST and SGST for Telangana (9% each)
                $cgst = $stock->total * 0.09; // CGST value (9% of total)
                $sgst = $stock->total * 0.09; // SGST value (9% of total)
                // Calculate total with CGST and SGST
                $totalAmount = $stock->total + $cgst + $sgst;
            } else {
                // IGST for other states (18%)
                $igst = $stock->total * 0.18; // IGST value (18% of total)
                // Calculate total with IGST
                $totalAmount = $stock->total + $igst;
            }
        }

        return [
            'id' => $stock->id,
            'product_id' => $stock->product_id,
            'product_name' => $productName,
            'sku' => $stock->sku,
            'buying_price' => $stock->product->buying_price,
            'selling_price' => $stock->product->selling_price,
            'discount' => $stock->product->discount,
            'margin' => $margin,
            'subtotal' => $stock->subtotal,
            'tax' => $stock->tax,
            'final_price' => $stock->total,
            'order_serial_no' => $order ? $order->order_serial_no : null, // Get order serial number
            'order_datetime' => $order ? $order->order_datetime : null, // Get order datetime
            'order_type' => $orderTypeLabel, // Map order type to label
            'order_address' => $orderAddress ? [
                'city' => $orderAddress->city,
                'state' => $orderAddress->state,
                'postal_code' => $orderAddress->zip_code,
            ] : null, // Return order address details if available
            'cgst_amount' => round($cgst, 2), // CGST amount (rounded to 2 decimal places)
            'sgst_amount' => round($sgst, 2), // SGST amount (rounded to 2 decimal places)
            'igst_amount' => round($igst, 2), // IGST amount (rounded to 2 decimal places)
            'total_amount' => round($totalAmount, 2), // Total amount after tax calculation
        ];
    }),
]);


 



    }
    
    
    
    public function products(){
        
 
//  $stock = Stock::whereHas('product')->get();
// dd($stock);
        
        // $stock=Stock::with('product')->get();
        
        // return response()->json([
        //     'status'=>true,
        //     'data'=>$stock
        //     ]);
            
            $stock=Stock::with('product')->get();
            $productDetails = $stock->pluck('product.name');

return response()->json([
    'status' => true,
    'data' => $productDetails,
]);
            
//             $stock = Stock::with('product')->get();

// $productDetails = $stock->pluck('product');

// return response()->json([
//     'status' => true,
//     'data' => $productDetails,
// ]);
        
    }
    
    
        public function products1(){
        
 


$stock = Stock::with('product:id,name')->get();

$productDetails = $stock->pluck('product')->map(function($product) {
    return [
        'id' => $product->id,
        'name' => $product->name,
    ];
});


return response()->json([
    'status' => true,
    'data' => $productDetails,
]);
       
    
}
    
    
    
    public function generateA4Pdf($id)
{
    
    
    
    $data = [
        'date' => 'Jan 18, 2025',
        'op' => 174,
        'time' => 'Jan 18, 2025',
        'pet_type' => 'Cat',
        'pet_name' => 'Catty1qq',
        'uhid' => 174,
        'doctor_name' => 'YuvaKiran',
        'services' => [
            'Digital Radiography',
            'Medicated Bath',
            'General Bath',
            'Microchipping',
            'Spay & Neuter',
            'Rabies Titre Test',
            'ABST (Antibiotic Sensitivity Test)'
        ],
    ];

    $pdf = Pdf::loadView('pdf.patient-details', $data);
    return $pdf->download('patient-details.pdf');
}
    
    
    
    
    
      public function generateReceipt($id){

        $get_details=clinictransaction::with('user','pet')->where('id',$id)->first();
       // dd($get_details);
        $dr_details=appointment::with('doctor_details')->where('id',$get_details->aid)->get();
       // dd($get_details->payment_type);

    //  dd($get_details->aid);
        //   dd($get_details->payment_for);
        $paymentMode = $get_details->payment_mode == 1 ? 'Offline' : 'Online';
        //  dd($get_details->payment_mode);

        $data = [
            'InvoiceNo' => $get_details->id,
            'patient_name' => $get_details->pet->petname,
            'pet_category' => $get_details->pet->category,
            'dr_name' => $dr_details[0]->doctor_details->name,
            'phone' => $get_details->user->phone,
            'state' => $get_details->user->state,
            'city' => $get_details->user->city,
            'date' => $get_details->created_at->format('M d, Y h:i a'),
            'payment' => $get_details->payment_type,
            'payment_for' => $get_details->payment_for,
            'amount' => $get_details->amount,
        ];
    
        // $pdf = Pdf::loadView('pdf.myPDF', $data)
        // ->setOption('font', 'Noto Sans');
    
        // return $pdf->download('example.pdf');
        
            $pdf = PDF::loadView('pdf.receipt', $data)->setPaper([0, 0, 210, 297], 'portrait'); // A7 size in points
            
            // $pdf = PDF::loadView('pdf.receipt', $data)->setPaper([0, 0, 595, 420], 'portrait'); // 

      return response($pdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="receipt.pdf"');
        
    }
    
    
        public function generateReceipt_all($id){

        // $get_details=clinictransaction::with('user','pet')->where('id',$id)->first();
        
        $get_details=clinictransaction::with('user','pet')->where('uni_transaction_id',$id)->get();
        // $get_details=clinictransaction::with('user','pet')->where('aid',$id)->get();
     
    //dd($get_details[0]->created_at);
     
        $dr_details=appointment::with('doctor_details')->where('id',$get_details[0]->aid)->get();
      // dd($get_details);

    //  dd($get_details->aid);
        //   dd($get_details->payment_for);
        $paymentMode = $get_details[0]->payment_mode == 1 ? 'Offline' : 'Online';
        
        $paymentData="";
        $payment_type = strtolower(trim($get_details[0]->payment_type));
        
         if (in_array($get_details[0]->payment_type, ['card', 'upi', 'net banking'])) {
               $paymentData = 'ONLINE';
            } elseif (strtolower($get_details[0]->payment_type) === 'cash') {
               $paymentData = 'OFFLINE';
            } else {
                $paymentData = 'UNKNOWN'; // Optional: Handle unexpected payment types
            }
        
       //dd($get_details[0]->transactionid);  
        
        $status;
        if($get_details[0]->payment_for=='In_House'){
             $status=1; // for op billing 
        }
        else if ($get_details[0]->payment_for=='E-Consultation') {
             $status=0; // for e-consultancy
        }
        
        
        else{
             $status=2; // for service billing
        }
     
     //dd( $status);
        $data = [
            'InvoiceNo' => $get_details[0]->id,
            'patient_name' => $get_details[0]->pet->petname,
            'pet_category' => $get_details[0]->pet->category,
            'dr_name' => $dr_details[0]->doctor_details->name ?? null,
            'phone' => $get_details[0]->user->phone,
            'state' => $get_details[0]->user->state,
            'city' => $get_details[0]->user->city,
            'date' => $get_details[0]->created_at->format('M d, Y h:i a'),
            // 'payment' => $paymentData ."-". $get_details[0]->payment_type,
            'payment' =>  $get_details[0]->payment_type,
            'paymentData' =>  $paymentData,
            // 'payment_for' => $get_details->payment_for,
             'payment_for' => $get_details, 
             'status_payment' => $status, 
            // 'amount' => $get_details[0]->amount,
            'total_amount' => $get_details->sum('amount'),
        ];
    
        // $pdf = Pdf::loadView('pdf.myPDF', $data)
        // ->setOption('font', 'Noto Sans');
    
        // return $pdf->download('example.pdf');
        
    //         $pdf = PDF::loadView('pdf.receipt', $data)->setPaper([0, 0, 210, 297], 'portrait'); // A7 size in points
            
    //         // $pdf = PDF::loadView('pdf.receipt', $data)->setPaper([0, 0, 595, 420], 'portrait'); // 

    //   return response($pdf->output(), 200)
    //     ->header('Content-Type', 'application/pdf')
    //     ->header('Content-Disposition', 'inline; filename="receipt.pdf"');
        
        
        
        $pdf = PDF::loadView('pdf.receipt', $data)
    ->setPaper([0, 0, 300, 1200], 'portrait'); // 80mm width, 500mm height (adjust as needed)

// Return the PDF response
return response($pdf->output(), 200)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'inline; filename="receipt.pdf"');
        
    }
    
    
     public function generateReceipt_all_print($id){

        // $get_details=clinictransaction::with('user','pet')->where('id',$id)->first();
        
       // $get_details=clinictransaction::with('user','pet')->where('uni_transaction_id',$id)->get();
         $get_details=clinictransaction::with('user','pet')->where('aid',$id)->get();
     
    //dd($get_details[0]->created_at);
     
        $dr_details=appointment::with('doctor_details')->where('id',$get_details[0]->aid)->get();
      // dd($get_details);

    //  dd($get_details->aid);
        //   dd($get_details->payment_for);
        $paymentMode = $get_details[0]->payment_mode == 1 ? 'Offline' : 'Online';
        
        $paymentData="";
        $payment_type = strtolower(trim($get_details[0]->payment_type));
        
         if (in_array($get_details[0]->payment_type, ['card', 'upi', 'net banking'])) {
               $paymentData = 'ONLINE';
            } elseif (strtolower($get_details[0]->payment_type) === 'cash') {
               $paymentData = 'OFFLINE';
            } else {
                $paymentData = 'UNKNOWN'; // Optional: Handle unexpected payment types
            }
        
       //dd($get_details[0]->transactionid);  
        
        
        //  dd($get_details->sum('amount'));
      
        $status;
        $finalAmouny;
        $InhouseAmount=0;
        if($get_details[0]->payment_for=='In_House'){
             $status=1; // for op billing 
            //  $InhouseAmount=subservice::where('id',15)->get();
              $InhouseAmount=subservice::where('id',15)->value('fee');
            //  $finalAmouny=$get_details->sum('amount')+$InhouseAmount;
             
        }
        else if ($get_details[0]->payment_for=='E-Consultation') {
             $status=0; // for e-consultancy
             
              $InhouseAmount=subservice::where('id',28)->value('fee');
            //  $finalAmouny=$get_details->sum('amount')+$InhouseAmount;
        }
        
        
        else{
             $status=2; // for service billing
        }
     
    // dd( $InhouseAmount);
     
    // dd($get_details->sum('amount')-500);
     
     
//  dd($finalAmouny);
        $data = [
            'InvoiceNo' => $get_details[0]->id,
            'patient_name' => $get_details[0]->pet->petname,
            'pet_category' => $get_details[0]->pet->category,
            'dr_name' => $dr_details[0]->doctor_details->name ?? null,
            'phone' => $get_details[0]->user->phone,
            'state' => $get_details[0]->user->state,
            'city' => $get_details[0]->user->city,
            'date' => $get_details[0]->created_at->format('M d, Y h:i a'),
            // 'payment' => $paymentData ."-". $get_details[0]->payment_type,
            'payment' =>  $get_details[0]->payment_type,
            'paymentData' =>  $paymentData,
            // 'payment_for' => $get_details->payment_for,
             'payment_for' => $get_details, 
             'status_payment' => $status, 
            // 'amount' => $get_details[0]->amount,
            'total_amount' => $get_details->sum('amount')-$InhouseAmount,
            // 'total_amount' => "Gopi",
            // 'total_amount' =>$finalAmouny,
        ];
    
        // $pdf = Pdf::loadView('pdf.myPDF', $data)
        // ->setOption('font', 'Noto Sans');
    
        // return $pdf->download('example.pdf');
        
    //         $pdf = PDF::loadView('pdf.receipt', $data)->setPaper([0, 0, 210, 297], 'portrait'); // A7 size in points
            
    //         // $pdf = PDF::loadView('pdf.receipt', $data)->setPaper([0, 0, 595, 420], 'portrait'); // 

    //   return response($pdf->output(), 200)
    //     ->header('Content-Type', 'application/pdf')
    //     ->header('Content-Disposition', 'inline; filename="receipt.pdf"');
        
        
        
        $pdf = PDF::loadView('pdf.receipt', $data)
    ->setPaper([0, 0, 300, 1200], 'portrait'); // 80mm width, 500mm height (adjust as needed)

// Return the PDF response
return response($pdf->output(), 200)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'inline; filename="receipt.pdf"');
        
    }
    
    
    public function generateReceipt11()
        {
           
            
    $data = [
        'patient_name' => 'John Doe',
        'appointment_date' => '2024-12-26',
        'appointment_type' => 'General Checkup',
        'doctor_name' => 'Dr. Smith',
        'services' => [
            ['name' => 'Consultation', 'qty' => 1, 'price' => 50],
            ['name' => 'Blood Test', 'qty' => 1, 'price' => 30],
        ],
        'total' => 80,
    ];

    $pdf = PDF::loadView('pdf.receipt', $data)->setPaper([0, 0, 210, 297], 'portrait'); // A7 size in points

      return response($pdf->output(), 200)
        ->header('Content-Type', 'application/pdf')
        ->header('Content-Disposition', 'inline; filename="receipt.pdf"');

    // return $pdf->stream('receipt.pdf'); // Or use ->download('receipt.pdf')
           
           
        }
            
    
    
    
    public function cancel_appointment($id){
          
        
        try {
            $check_status = Appointment::findOrFail($id);
        
            if ($check_status->status !=0) {
                return response()->json(['message' => 'Appointment is already confirmed']);
            }
        
            // Update the status
            $check_status->status = 3;
            $check_status->status_details = 'Appointment Cancelled';
            $check_status->save();
        
            return response()->json(['message' => 'Appointment Cancelled Successfully']);
        } catch (ModelNotFoundException $e) {
            return response()->json(['error' => 'Appointment not found'], 404);
        }
        
        
        
        
        
        
        
        
        
        
        // dd($check_status);
        
        // if($check_status){
        //         return response()->json([
        //         'status'=>false,
        //         'message'=>'Invalid Appointment id or Appointment Already confirmed...!'
        //         ]);
        // }
        
        
        
        // if($check_status && $check_status->status==0){
                
        //     $check_status->status=1;
        //     $check_status->save;
            
        //     return response()->json([
        //         'status'=>true,
        //         'message'=>'Appointment Cancelled Successfully...!'
        //         ]);
            
        // }else{
            
        //      return response()->json([
        //         'status'=>false,
        //         'message'=>'Invalid Appointment id or Appointment Already confirmed...!'
        //         ]);
            
            
        // }
        
        
    
        
    }
    
    public function get_fee_details($id){
        
        $get_fee=subservice::where('id',$id)->get();
        
       // dd($get_fee[0]->id);
       
        if($get_fee[0]->id==28){
             return response()->json([
            'status'=>true,
            'data'=>$get_fee,
             'payment'=>'Online'        
         ]);        
        }else if($get_fee[0]->id==15){
             return response()->json([
            'status'=>true,
            'data'=>$get_fee,
             'payment'=>'Offline'        
         ]); 
            
        }
            
       
        
    }
    
    
    
    public function get_time_slots(Request $request)
{
   
   
    $timeRanges = [
        ['start' => '09:00', 'end' => '13:00'], // Morning: 9:00 AM - 1:00 PM
        ['start' => '14:00', 'end' => '18:00'], // Afternoon: 2:00 PM - 6:00 PM
    ];

    $interval = 15; // Grace period in minutes
    $slots = [];

    foreach ($timeRanges as $range) {
        $startTime = Carbon::createFromFormat('H:i', $range['start']);
        $endTime = Carbon::createFromFormat('H:i', $range['end']);

        while ($startTime->lt($endTime)) {
            $slots[] = $startTime->toTimeString(); // Add start time
            $startTime->addMinutes($interval);    // Increment by 15 minutes
        }
    }

    return response()->json([
        'status'=>true,
        'time_slots'=>$slots
        ]
        
        );
    
    // return response()->json($slots);

}
    

    public function list_user_category($id){

        $get_my_pet_category=mypet::where('id',$id)->get();

        return response()->json([
            'status'=>true,
            'data'=>$get_my_pet_category
        ]);

    }

    public function user_pets(){
        $get_pets_details=mypet::where('user_assigned',Auth()->user()->id)->get();

        $get_consultation_fee= subservice::where('id',15)->value('fee');

        if(count($get_pets_details)>0){
            return response()->json([
                'status'=>true,
                'data'=>$get_pets_details,
                'Consultation_fee'=>$get_consultation_fee,
                'message'=>'Data Available'
              
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'data'=>$get_pets_details,
                'message'=>'No Pets Available'
            ]);
        }

       

    }


    public function search_by_date(Request $request){
        // dd($date);
        // dd(Auth()->user()->id);
        //  $list_of_appointment=appointment::whereDate('dateofapp',$date)->where('user_id',Auth()->user()->user_id )->get();
            // Retrieve the 'date' query parameter
            $date = $request->query('date');
        //     // Use the date parameter to perform your query
        //     $appointments = Appointment::whereDate('dateofapp', $date)->get();
        //     // Return the result
        //     return response()->json($appointments);
        // }
         $list_of_appointment=appointment::whereDate('dateofapp',$date)->where('user_id',Auth()->user()->id)->get();
         if(count($list_of_appointment)>0){
             return response()->json([
                 'status'=>true,
                 'data'=>$list_of_appointment
             ]);
         }else{
             return response()->json([
                 'status'=>false,
                 'data'=>$list_of_appointment
             ]);
         }
 }
    public function search_by_op_no($id){
        // dd(Auth()->user()->id);
         $list_of_appointment=appointment::where('id',$id)->get();
         if(count($list_of_appointment)>0){
             return response()->json([
                 'status'=>true,
                 'data'=>$list_of_appointment
             ]);
         }else{
             return response()->json([
                 'status'=>false,
                 'data'=>$list_of_appointment
             ]);
         }
 }
    public function add_all_appointment(){
        // dd(Auth()->user()->id);
         $list_of_appointment=appointment::get();
         if(count($list_of_appointment)>0){
             return response()->json([
                 'status'=>true,
                 'data'=>$list_of_appointment
             ]);
         }else{
             return response()->json([
                 'status'=>false,
                 'data'=>$list_of_appointment
             ]);
         }
 }
 public function add_serch_by_id_appointment($id){
    // $list_of_appointment=appointment::get();
    $list_of_appointment=appointment::where('user_id',$id)->get();
    if(count($list_of_appointment)>0){
        return response()->json([
            'status'=>true,
            'data'=>$list_of_appointment
        ]);
    }else{
        return response()->json([
            'status'=>false,
            'data'=>$list_of_appointment
        ]);
    }
 }
    public function add_user_appointment(Request $request){
            // dd(Auth()->user()->id);
           $date = $request->query('date');
           $search = $request->query('search');

            $today = Carbon::today()->toDateString();
            
            $today_appointment=appointment::where('user_id', Auth()->user()->id)->whereDate('dateofapp', $today)->count();
           
            $past_date = Carbon::today()->subDay()->toDateString();

           

           
            $futureAppointments = Appointment::where('dateofapp', '>', $today)->where('user_id', Auth()->user()->id)->count();

           // dd(futureAppointments);

            $past_appointment = appointment::where('user_id', Auth()->user()->id)
                ->whereDate('dateofapp', $past_date)
                ->count();

    

        

           // Initialize the query for appointments
           $query = appointment::where('user_id', Auth()->user()->id);
           // If a date parameter is provided, filter by the date
           if ($date !== null) {
               $query->whereDate('dateofapp', $date);
           }
           // If a search parameter is provided, filter based on the search term
           if ($search !== null) {
               // Assuming you want to search by `name` or `description` field in the appointment
               $query->where(function ($q) use ($search) {
                   $q->where('petname', 'like', '%' . $search . '%');
                    //  ->orWhere('description', 'like', '%' . $search . '%');
               });
           }
           // Get the list of appointments based on the filtered query
           $list_of_appointment = $query->get();
           // Return the results
           if (count($list_of_appointment) > 0) {
               return response()->json([
                   'status' => true,
                   'data' => $list_of_appointment,
                   'today_appointment'=>$today_appointment,
                   'past_appointment'=>$past_appointment,
                   'futureAppointments'=>$futureAppointments
               ]);
           } else {
               return response()->json([
                   'status' => false,
                   'data' => $list_of_appointment,
               ]);
           }
    }
    public function past_appointment(Request $request){
        $search = $request->query('search');
        $query = appointment::where('user_id', Auth()->user()->id)
                     ->whereDate('dateofapp', '<', Carbon::today());
        // If a search parameter is provided, filter based on the search term
        if ($search !== null) {
            // Assuming you want to search by `name` or `description` field in the appointment
            $query->where(function ($q) use ($search) {
                $q->where('petname', 'like', '%' . $search . '%');
                 //  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }
        // Get the list of appointments based on the filtered query
        $list_of_appointment = $query->get();
        // Return the results
        if (count($list_of_appointment) > 0) {
            return response()->json([
                'status' => true,
                'data' => $list_of_appointment,
            ]);
        } else {
            return response()->json([
                'status' => false,
                'data' => $list_of_appointment,
            ]);
        }
 }
 public function future_appointment(Request $request){
    $search = $request->query('search');
    $query = appointment::where('user_id', Auth()->user()->id)
                 ->whereDate('dateofapp', '>', Carbon::today());
    // If a search parameter is provided, filter based on the search term
    if ($search !== null) {
        // Assuming you want to search by `name` or `description` field in the appointment
        $query->where(function ($q) use ($search) {
            $q->where('petname', 'like', '%' . $search . '%');
             //  ->orWhere('description', 'like', '%' . $search . '%');
        });
    }
    // Get the list of appointments based on the filtered query
    $list_of_appointment = $query->get();
    // Return the results
    if (count($list_of_appointment) > 0) {
        return response()->json([
            'status' => true,
            'data' => $list_of_appointment,
        ]);
    } else {
        return response()->json([
            'status' => false,
            'data' => $list_of_appointment,
        ]);
    }
}
  
  
    public function add_appointment(appointmentRequest $req){
        //dd(Auth()->user()->id);
        
             $date = Carbon::parse($req->dateofapp);
            $dayOfWeek = $date->dayOfWeek;
        
            // Generate random time based on the day of the week
            if ($dayOfWeek == 0) { // Sunday (0)
            //dd("Sunday");
                // Random time between 10 AM and 12 PM (e.g., 10:30 AM)
                $randomTime = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . rand(10, 11) . ':' . rand(0, 59) . ':00');
            } else { // Monday to Saturday (1-6)
            
           // dd("week day");
                // Random time between 6 PM and 9 PM
                $randomTime = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . rand(18, 19) . ':' . rand(0, 59) . ':00');
            }
        
        
        
        $create_appointment=appointment::create([
       
            'user_id'=>Auth()->User()->id,
            'pet_id'=>$req->petname,
            'dateofapp'=> $randomTime,
            'amount'=>$req->amount,
            'payment'=>$req->payment,
            'status_details'=>env('STATUS_CREATED'),
            'app_type'=>$req->appointment_type,
            // 'categorie'=>$req->categorie,
          
        ]);
        if($create_appointment){
            return response()->json([
                'status'=>true,
                'message'=>'Appotinemnt Created'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Appotinemnt Not Created'
            ]);
        }
    }
    
   
   
   
    public function add_appointment_admin(Request $req){
        
        
    $rules = [
        'phone'      => 'required|numeric|exists:users,phone',
       
    ];
    // Step 2: Custom Error Messages (Optional)
    $messages = [
        'phone.required' => 'The phone number is required.',
        'phone.numeric' => 'The phone number must be numeric.',
        'phone.exists' => 'The phone number is not registered.',
        
    ];

    // Step 3: Validate Data
    $validator = Validator::make($req->all(), $rules, $messages);

    if ($validator->fails()) {
        // Custom failed validation response
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors(),
            ], 200)
        );
    }
        
        
        
        
        $check_mobile=user::where('phone',$req->phone)->get();
        
 
        
        if(count($check_mobile)>0){
            
            $id=$check_mobile[0]->id;
            $name=$check_mobile[0]->name;
            $email=$check_mobile[0]->email;
            $phone=$check_mobile[0]->phone;
        
             $details=compact('id','name','email','phone');
        
        
             $check_pet_details=mypet::where('user_assigned',$check_mobile[0]->id)->get();
        
        
        return response()->json([
            'status'=>true,
            'details'=>$details,
            'pets'=>$check_pet_details
            
            
            ]);
            
            
        }else{
            
        //    dd("no user");
            
             return response()->json([
            'status'=>false,
            'message'=>'No user Found',
           
            
            
            ]);
             
            
            
            
        }
            
        
    }
    
    
    
    public function book_appointment(Request $req){
      
      
        
   
      
        

     $rules = [
        'user_id'      => 'required|numeric|exists:users,id',
        'petname'=>'required',
        'dateofapp'=>'required|date_format:Y-m-d H:i:s|after_or_equal:today',
        // 'dateofapp'=>'required|date_format:Y-m-d|after_or_equal:today',
        'amount'=>'required|numeric',
        'payment'=>'required',
        'appointment_type'=>'required|in:15,28',
          
       
      
    ];

    // Step 2: Custom Error Messages (Optional)
    $messages = [
        'user_id.required' => 'User Id required.',
        'petname.required' => 'Pet Id Required.',
       
    ];

    // Step 3: Validate Data
    $validator = Validator::make($req->all(), $rules, $messages);
    
    
    
    

    if ($validator->fails()) {
        // Custom failed validation response
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

        $date = Carbon::parse($req->dateofapp);
          $dayOfWeek = $date->dayOfWeek;
        
            // Generate random time based on the day of the week
            if ($dayOfWeek == 0) { // Sunday (0)
                // Random time between 10 AM and 12 PM (e.g., 10:30 AM)
                $randomTime = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . rand(10, 11) . ':' . rand(0, 59) . ':00');
            } else { // Monday to Saturday (1-6)
                // Random time between 6 PM and 9 PM
                $randomTime = Carbon::createFromFormat('Y-m-d H:i:s', $date->format('Y-m-d') . ' ' . rand(18, 19) . ':' . rand(0, 59) . ':00');
            }
        
        
        
        
         $create_appointment=appointment::create([
           

            'user_id'=>$req->user_id,
            'pet_id'=>$req->petname,
            // 'dateofapp'=>$req->dateofapp,
            'dateofapp'=>$randomTime,
            'amount'=>$req->amount,
            'payment'=>$req->payment,
            'status_details'=>env('STATUS_CREATED'),
            'app_type'=>$req->appointment_type,
            // 'categorie'=>$req->categorie,
          
        ]);
        if($create_appointment){
            return response()->json([
                'status'=>true,
                'message'=>'Appotinemnt Created'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Appotinemnt Not Created'
            ]);
        }
        
        
        
    }
    
    
    
    public function add_pet_admin(Request $req){
        
        
        
    $rules = [
        'phone'      => 'required|numeric|exists:users,phone',
        'petname'    => 'required|string|max:255',
        'petgender'  => 'required|in:M,F',
        'petbread'   => 'required|string|max:255',
        'category'   => 'required|string|max:255',
        'age'     => 'required|integer|min:0|max:20',
        
   
            'dateOfBirth' => 'required|date|before_or_equal:today',
            'petAgeOptions' => 'required|in:Days,Months,Years',
            
        
        
    ];

    // Step 2: Custom Error Messages (Optional)
    $messages = [
        'phone.required' => 'The phone number is required.',
        'phone.numeric' => 'The phone number must be numeric.',
        'phone.exists' => 'The phone number is not registered.',
        'petname.required' => 'The pet name is required.',
        'petgender.in' => 'Pet gender must be male or female.',
        
        'dateOfBirth.required' => 'Pet Date of Birth Required.',
        'dateOfBirth.date' => 'Pet Date of Birth should be in correct format.',
        'petAgeOptions.required' => 'Select Pet Age Details.',
        'petAgeOptions.in' => 'Pet Age should be in Days or Months Or Years.',
        
        'petgender.in' => 'Pet gender must be male or female.',
    ];

    // Step 3: Validate Data
    $validator = Validator::make($req->all(), $rules, $messages);

    if ($validator->fails()) {
        // Custom failed validation response
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation Errors',
                'errors' => $validator->errors()->first(),
            ], 200)
        );
    }
        
        
        
            
        
        
        
        
          $check_mobile=user::where('phone',$req->phone)->get();
          
          
          if(count($check_mobile)>0){
             // dd($check_mobile[0]->id);
             
         $create_pet=mypet::create([
            'petname'=>$req->petname,
            'petgender'=>$req->petgender,
            'petbread'=>$req->petbread,
            'category'=>$req->category ,
            'petAge'=>$req->petAge ,
            'user_assigned'=>$check_mobile[0]->id,
        ]);


        if($create_pet){

            return response()->json([
                'status'=>true,
                'message'=>'Pet Added Successfully...!'
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Something went wrong please try again later'
            ]);
        }
             
             
             
             
             
             
             
             
             
             
             
             
             
            
                
             
             
             
          }else{
              
              return response()->json([
                  'status'=>false,
                  'message'=>'No User Registred'
                  
                  
                  
                  ]);
          }
          
          //dd($check_mobile[0]->id);
        
    }
    
    
    
    
    
    
    
}
