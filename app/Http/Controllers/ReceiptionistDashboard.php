<?php
namespace App\Http\Controllers;
use App\Models\labreport;
use App\Models\service;
use Illuminate\Http\Request;
use App\Models\appointment;
use App\Models\clinictransaction;
use App\Models\user;
use App\Models\prescription;
use App\Models\mypet;
use App\Models\subservice;
use App\Models\medicaine;
use App\Models\medicanprecreprion;
use Carbon\Carbon;
use App\Http\Requests\appointmentconfirmedRequest;
use App\Http\Requests\reportsRequest;
use App\Http\Requests\preceiptionRequest;
use DB;
use Illuminate\Http\Exceptions\HttpResponseException;


use Barryvdh\DomPDF\Facade\Pdf;
class ReceiptionistDashboard extends Controller
{
    //
    
        public function addMedicaine(Request $req){
             $rules = [
                    'medicaineName' => 'required|string', 
                    'medicaineType' => 'required|string|in:Tab,Syr,Inj', 
                      
                ];
            
                // Define custom messages
                $messages = [
                    'medicaineName.required' => 'Medicain Name Required',
                    'medicaineType.in' => 'Medicain Type Should be Tab Or Syr Or Inj',
                   
                ];
            
                // Perform validation
                $validator = \Validator::make($req->all(), $rules, $messages);
            
                if ($validator->fails()) {
                    // Handle failed validation
                    throw new HttpResponseException(
                        response()->json([
                            'status' => false,
                            'message' => $validator->errors()->first(),
                        ])
                    );
                }
                
               // dd(Auth()->user());
                
                $create_medicain=medicaine::create([
                    'medicaineName'=>$req->medicaineName,
                    'medicaineType'=>$req->medicaineType
                    
                    ]);
                    
                if($create_medicain){
                        return response()->json([
                            'status'=>true,
                            'message'=>'Medicaine Created Successfully',
                            ]);
                    
                }else{
                     return response()->json([
                            'status'=>false,
                            'message'=>'Something went wrong please try again later',
                            ]);
                    
                    
                }        
                
            
            
        }
        
        public function getMedicaine(Request $req){
            
          // dd("DSfjsdf");
               $type = $req->query('type');
              
               if ($type === 'all') {
                   
                    // $all_medicanes=medicaine::where('status',1)->get();
                    $all_medicanes=medicaine::all();
            
                    return response()->json([
                        'status'=>true,
                        'data'=>$all_medicanes
                        ]);
                   
                    
                }else if($type === 'active'){
                    
                    $all_medicanes=medicaine::where('status',1)->get();
            
                    return response()->json([
                        'status'=>true,
                        'data'=>$all_medicanes
                        ]);
                    
                }else if($type === 'in-active'){
                   $all_medicanes=medicaine::where('status',0)->get();
            
                    return response()->json([
                        'status'=>true,
                        'data'=>$all_medicanes
                        ]);
                }
                
               
            
           
        }
        
        public function getMedicaineByid($id){
            $medicaines=medicaine::where('id',$id)->first();
            
            return response()->json([
                'status'=>true,
                'data'=>$medicaines
                ]);
        }
        
        public function updateMedicaine(Request $req,$id){
           
             $rules = [
                    'medicaineName' => 'required|string', 
                    'medicaineType' => 'required|string|in:Tab,Syr,Inj', 
                    'status' => 'nullable|in:0,1', 
                      
                ];
            
                // Define custom messages
                $messages = [
                    'medicaineName.required' => 'Medicain Name Required',
                    'medicaineType.in' => 'Medicain Type Should be Tab Or Syr Or Inj',
                    'status.in' => 'Medicain Status Should be 0 for Inactive Or 1 for Active',
                   
                ];
            
                // Perform validation
                $validator = \Validator::make($req->all(), $rules, $messages);
            
                if ($validator->fails()) {
                    // Handle failed validation
                    throw new HttpResponseException(
                        response()->json([
                            'status' => false,
                            'message' => $validator->errors()->first(),
                        ])
                    );
                } 
                
                if($req->status==null){
                          $update_medicaines=medicaine::where('id',$id)->update([
                            'medicaineName'=>$req->medicaineName,
                            'medicaineType'=>$req->medicaineType,
                            ]);    
                    
                            if($update_medicaines==1){
                                return response()->json(['status'=>true,'message'=>'Medicaine Details updated']);
                            }  else{
                                 return response()->json(['status'=>false,'message'=>'Something went wrong please try again later']);
                            }      
                    
                }else{
                    
                     $update_medicaines=medicaine::where('id',$id)->update([
                            'medicaineName'=>$req->medicaineName,
                            'medicaineType'=>$req->medicaineType,
                            'status'=>$req->status
                            ]);    
                    
                            if($update_medicaines==1){
                                return response()->json(['status'=>true,'message'=>'Medicaine Details updated']);
                            }  else{
                                 return response()->json(['status'=>false,'message'=>'Something went wrong please try again later']);
                            }  
                    
                    
                }
                
              
            
        }
    
    
    
        public function add_services(Request $req){
            
             // dd(Auth()->user());
                $rules = [
                    'service_name' => 'required|string', // Ensure it's an array with at least one ID
                      
                ];
            
                // Define custom messages
                $messages = [
                    'service_name.required' => 'Service Name Required',
                   
                ];
            
                // Perform validation
                $validator = \Validator::make($req->all(), $rules, $messages);
            
                if ($validator->fails()) {
                    // Handle failed validation
                    throw new HttpResponseException(
                        response()->json([
                            'status' => false,
                            'message' => $validator->errors()->first(),
                        ])
                    );
                }
                
                
              
                
                
                $create_service=service::create([
                    'service_name'=>$req->service_name
                    
                    ]);
                    
                if($create_service){
                    
                    return response()->json([
                        'status'=>true,
                        'message'=>'New Service Created'
                        
                        ]);
                }else{
                    
                      return response()->json([
                        'status'=>false,
                        'message'=>'Something went wrong please try again'
                        
                        ]);
                    
                }        

            
            
            
        }
    
    
        public function list_services(){
            
            $services=service::get();
            
            return response()->json([
                'status'=>true,
                'data'=>$services
                ]);
        }
    
    
        public function get_services($id){
            
             $services=service::where('id',$id)->get();
            
            return response()->json([
                'status'=>true,
                'data'=>$services
                ]);
            
            
        }
        
        
        public function update_services(Request $req,$id){
            
             $rules = [
                    'service_name' => 'required|string', // Ensure it's an array with at least one ID
                      
                ];
            
                // Define custom messages
                $messages = [
                    'service_name.required' => 'Service Name Required',
                   
                ];
            
                // Perform validation
                $validator = \Validator::make($req->all(), $rules, $messages);
            
                if ($validator->fails()) {
                    // Handle failed validation
                    throw new HttpResponseException(
                        response()->json([
                            'status' => false,
                            'message' => $validator->errors()->first(),
                        ])
                    );
                }
                
                $update_serive=service::where('id',$id)->update([
                    'service_name'=>$req->service_name
                    
                    ]);
                    
                if($update_serive==1){
                    
                    return response()->json([
                        'status'=>'true',
                        'message'=>'Service Name Updated'
                        
                        ]);
                }else{
                    return response()->json([
                        'status'=>'false',
                        'message'=>'Something went try please try again later'
                        
                        ]);
                    
                }    
            
        }
        
        
        public function add_sub_services(Request $req){
              $rules = [
                    'subservicename' => 'required|string', // Ensure it's an array with at least one ID
                    'service_id' => 'required|integer', // Ensure it's an array with at least one ID
                    'fee' => 'required|integer', // Ensure it's an array with at least one ID
                      
                ];
            
                // Define custom messages
                $messages = [
                    'subservicename.required' => 'Sub Service Name Required',
                    'service_id.required' => 'Service Id  Required',
                    'fee.required' => 'Fee amount  Required',
                   
                ];
            
                // Perform validation
                $validator = \Validator::make($req->all(), $rules, $messages);
            
                if ($validator->fails()) {
                    // Handle failed validation
                    throw new HttpResponseException(
                        response()->json([
                            'status' => false,
                            'message' => $validator->errors()->first(),
                        ])
                    );
                }
                
                
                $existofServiceID=service::where('id',$req->service_id)->first();
                
                if(!$existofServiceID){
                    return response()->json([
                        'status'=>false,
                        'message'=>'Invalid Service Id'
                        ]);
                }
                
                
                $create_sub_service=subservice::create([
                    'subservicename'=>$req->subservicename,
                    'service_id'=>$req->service_id,
                    'fee'=>$req->fee,
                    ]);
                
                if($create_sub_service){
                    return response()->json([
                        'status'=>true,
                        'message'=>'Subservice added'
                        
                        ]);
                }else{
                    
                     return response()->json([
                        'status'=>false,
                        'message'=>'Something went wrong please try again later'
                        
                        ]);
                }
        }

        public function download_reports($id){

         
            $report = labreport::find($id);

                if (!$report || !$report->filepath) {
                    return response()->json(['error' => 'File not found.'], 404);
                }

                // Construct the full path to the file using the relative path from the database
                $filePath = public_path($report->filepath); // Resolves to `D:\livewires\Drvenky\public\storage\uploads\filename.pdf`

                // Check if the file exists
                if (!file_exists($filePath)) {
                    return response()->json(['error' => 'File does not exist on the server.'], 404);
                }

                // Return the file as a downloadable response
                return response()->download($filePath);

        }


        public function upload_reports(reportsRequest $req){

         

            if ($req->hasFile('file_upload')) {
                $file = $req->file('file_upload');
            
                // Retrieve the current record to get the existing file path
                $report = labreport::find($req->id);
            
                if ($report && $report->filepath) {
                    // Construct the full path to the existing file
                    $existingFilePath = public_path($report->filepath); // e.g., D:\livewires\Drvenky\public\storage\uploads\old_filename.pdf
                    
                    // Check if the file exists and delete it
                    if (file_exists($existingFilePath)) {
                        unlink($existingFilePath); // Delete the existing file
                    }
                }
            
                // Save the new file to the public storage/uploads directory
                $newFileName = $file->getClientOriginalName(); // Get the original filename
                $path = $file->move(public_path('storage/uploads'), $newFileName); // Save to `D:\livewires\Drvenky\public\storage\uploads`
            
                // Save the relative path to the database
                $relativePath = 'storage/uploads/' . $newFileName; // Relative path for the database
            
                // Update the database with the new file path
                labreport::where('id', $req->id)->update([
                    'filepath' => $relativePath, // Update the file path in the database
                ]);
            
                return response()->json(['success' => 'File uploaded successfully!', 'path' => $relativePath]);
            }
            






            // if ($req->hasFile('file_upload')) {
            //     $file = $req->file('file_upload');
            
            //     // Retrieve the existing file path from the database
            //     $existingReport = labreport::find($req->id);
            
            //     if ($existingReport && $existingReport->filepath) {
            //         // Check if the file exists in the storage and delete it
            //         $existingFilePath = 'public/' . $existingReport->filepath; // Ensure correct storage path
            //         if (\Storage::exists($existingFilePath)) {
            //             \Storage::delete($existingFilePath);
            //         }
            //     }
            
            //     // Save the new file to a specific directory
            //     $path = $file->store('uploads', 'public'); // Stores in `storage/app/public/uploads`
            
            //     // Update the database with the new file path
            //     $existingReport->update([
            //         'filepath' => $path,
            //     ]);
            
            //     return response()->json(['success' => 'File uploaded successfully!', 'path' => $path]);
            // }
            
         
         
            // if ($req->hasFile('file_upload')) {
            //     $file = $req->file('file_upload');
        
            //     // Save the file to a specific directory
            //     $path = $file->store('uploads', 'public'); // Stores in `storage/app/public/uploads`
        
            //      $upload_reports=labreport::where('id',$req->id)->update([
            //         'filepath'=> $path
            //      ]);   
                



            //     return response()->json(['success' => 'File uploaded successfully!', 'path' => $path]);
            // }


        


        }


        public function lab_reports(Request $request){

                // $get_appoint=appointment::where('id',$id)->get();
                // dd($get_appoint);

                $appointmentid = $request->query('aid');
                $serviceName = $request->query('serviceName');

                if ($appointmentid) {
                    $get_data=labreport::with('appiontment','user','pet','getAllSubservices')->where('aid',$appointmentid)->get();
                    $total_reports=labreport::with('appiontment','user','pet','getAllSubservices')->where('aid',$appointmentid)->count();
                    $pending_reports=labreport::with('appiontment','user','pet','getAllSubservices')->where('aid',$appointmentid)->where('filepath',null)->count();
                    $complete_reports=labreport::with('appiontment','user','pet','getAllSubservices')->where('aid',$appointmentid)->where('filepath','!=',null)->count();
                    //dd($complete_reports);
                
                    return response()->json([
                        'status'=>true,
                        'data'=>$get_data,
                        'total_reports'=>$total_reports,
                        'pending_reports'=>$pending_reports,
                        'complete_reports'=>$complete_reports,
                    ]);


                }


            
                $get_data=labreport::with('appiontment','user','pet','getAllSubservices')->get();
                $total_reports=labreport::with('appiontment','user','pet','getAllSubservices')->count();
                 $pending_reports=labreport::with('appiontment','user','pet','getAllSubservices')->where('filepath',null)->count();
                  $complete_reports=labreport::with('appiontment','user','pet','getAllSubservices')->where('filepath','!=',null)->count();
                
                    return response()->json([
                        'status'=>true,
                        'data'=>$get_data,
                        'total_reports'=>$total_reports,
                        'pending_reports'=>$pending_reports,
                        'complete_reports'=>$complete_reports,
                    ]);

        }


        
             public function confirmation_prescription11($id) {


            // $prescriptions = prescription::find($id); // Get the prescription
            $prescriptions1 = prescription::where('aaid',$id)->get(); // Get the prescription

            // dd($prescriptions[0]->id);
            $prescriptions = prescription::find($prescriptions1[0]->id); // Get the prescription

            //  dd($prescriptions1[0]->id);    

            if($prescriptions->payment_status==1){
                // payment already done

                return response()->json([
                    'status'=>false,
                    'message' => 'Already Done'
                ]);


            }else{

                $allSubservices = $prescriptions->getAllSubservicesWithFees();
          
         //   dd($allSubservices);

            $subservices_inhouse = $prescriptions->relatedInhouseSubservices();  // Call the method directly
            $subservices_grooming = $prescriptions->relatedgroomingSubservices();  // Call the method directly
            $subservices_general_services = $prescriptions->relatedservicesSubservices();  // Call the method directly
            $subservices_lab = $prescriptions->relatedlabSubservices();  // Call the method directly

            $user=prescription::with('appointment_details','user','pet')->where('id',$prescriptions1[0]->id)->get();
            

            //    dd(Auth()->user()->id); 

            
            foreach ($allSubservices['subserviceData'] as $key => $service) {
                // Validate service fields

                // dd($service);
                //dd($allSubservices['subserviceData']);
                if (isset($service['subservicename'], $service['fee'], $service['id'])) {

                    try {
                        // Create clinictransaction entry
                        $add_details = clinictransaction::create([
                            'payment_mode' => 1, 
                            'payment_for' => $service['subservicename'],
                            'amount' => $service['fee'],
                            'paid_by_id' => Auth()->user()->id,
                            'aid' => $user[0]->aaid ?? null, // Use null coalescing to prevent errors
                            'uid' => $user[0]->user->id ?? null,
                            'pid' => $user[0]->pet->id ?? null,
                        ]);
            

                        // Create labreport entry
                        $assign_labs = labreport::create([
                            'aid' => $user[0]->aaid ?? null, 
                            'pid' => $user[0]->pet->id ?? null, 
                            'uid' => $user[0]->user->id ?? null, 
                            'ssid' => $service['id'], 
                            'ssid_name' => $service['subservicename'], 
                            'statusid' => 1,

                        ]);


                       

                    } catch (\Exception $e) {
                        // Log the error for debugging
                        \Log::error('Error creating records: ' . $e->getMessage());
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                } else {

                    dd("'Missing required fields in service: ");
                    \Log::warning('Missing required fields in service: ' . json_encode($service));
                }
            }
            


            $chage_statusp=prescription::where('id',$prescriptions1[0]->id)->update([
                'payment_status'=>true
            ]);

            $chage_status=appointment::where('id',$id)->update([
                'lab_payments'=>1
            ]);


            return response()->json([
                'status'=>true,
                'message' => 'Transaction details saved successfully!'
            ]);



            }

               
        }
            


        public function confirmation_prescription($id) {


            // $prescriptions = prescription::find($id); // Get the prescription
            $prescriptions1 = prescription::where('aaid',$id)->get(); // Get the prescription

            // dd($prescriptions[0]->id);
            $prescriptions = prescription::find($prescriptions1[0]->id); // Get the prescription

            //  dd($prescriptions1[0]->id);    

            if($prescriptions->payment_status==1){
                // payment already done

                return response()->json([
                    'status'=>false,
                    'message' => 'Already Done'
                ]);


            }else{

                $allSubservices = $prescriptions->getAllSubservicesWithFees();
          
         //   dd($allSubservices);

            $subservices_inhouse = $prescriptions->relatedInhouseSubservices();  // Call the method directly
            $subservices_grooming = $prescriptions->relatedgroomingSubservices();  // Call the method directly
            $subservices_general_services = $prescriptions->relatedservicesSubservices();  // Call the method directly
            $subservices_lab = $prescriptions->relatedlabSubservices();  // Call the method directly

            $user=prescription::with('appointment_details','user','pet')->where('id',$prescriptions1[0]->id)->get();
            

            //    dd(Auth()->user()->id); 

            
            foreach ($allSubservices['subserviceData'] as $key => $service) {
                // Validate service fields

                // dd($service);
                //dd($allSubservices['subserviceData']);
                if (isset($service['subservicename'], $service['fee'], $service['id'])) {

                    try {
                        // Create clinictransaction entry
                        $add_details = clinictransaction::create([
                            'payment_mode' => 1, 
                            'payment_for' => $service['subservicename'],
                            'amount' => $service['fee'],
                            'paid_by_id' => Auth()->user()->id,
                            'aid' => $user[0]->aaid ?? null, // Use null coalescing to prevent errors
                            'uid' => $user[0]->user->id ?? null,
                            'pid' => $user[0]->pet->id ?? null,
                        ]);
            

                        // Create labreport entry
                        $assign_labs = labreport::create([
                            'aid' => $user[0]->aaid ?? null, 
                            'pid' => $user[0]->pet->id ?? null, 
                            'uid' => $user[0]->user->id ?? null, 
                            'ssid' => $service['id'], 
                            'ssid_name' => $service['subservicename'], 
                            'statusid' => 1,

                        ]);


                       

                    } catch (\Exception $e) {
                        // Log the error for debugging
                        \Log::error('Error creating records: ' . $e->getMessage());
                        return response()->json(['error' => $e->getMessage()], 500);
                    }
                } else {

                    dd("'Missing required fields in service: ");
                    \Log::warning('Missing required fields in service: ' . json_encode($service));
                }
            }
            


            $chage_statusp=prescription::where('id',$prescriptions1[0]->id)->update([
                'payment_status'=>true
            ]);

            $chage_status=appointment::where('id',$id)->update([
                'lab_payments'=>1
            ]);


            return response()->json([
                'status'=>true,
                'message' => 'Transaction details saved successfully!'
            ]);



            }


            
            

           

          

        
            // $get_preceiptions=prescription::with('pet','user','dr_user','appointment_details','relatedSubservices')->where('id',$id)->get();
            // return response()->json([
            //     'status'=>true,
               
            //     // 'prescriptions'=>$user,
            //     // 'inhouse'=>$subservices_inhouse,
            //     // 'grooming'=>$subservices_grooming,
            //     // 'general_services'=>$subservices_general_services,
            //     // 'Lab_services'=>$subservices_lab,
            //      'all'=>$allSubservices,
               
            // ]);

                    
        }
        
        
        public function getServices222(Request $request,$id)
{
    // Define validation rules
    
   // dd($id);
    
    $rules = [
        'service_ids' => 'required|array|min:1', // Ensure it's an array with at least one ID
        'service_ids.*' => 'integer',    
    ];

    // Define custom messages
    $messages = [
        'service_ids.required' => 'service_ids Required',
        'service_ids.array' => 'service_ids Required in array',
        'service_ids.min' => 'service_ids Min One',
    ];

    // Perform validation
    $validator = \Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        // Handle failed validation
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ])
        );
    }


     $serviceIds = $validator->validated()['service_ids'];
     
     //dd($serviceIds);
      $prescriptions1 = prescription::where('aaid',$id)->get(); // Get the prescription

            // dd($prescriptions[0]->id);
            $prescriptions = prescription::find($prescriptions1[0]->id); // Get the prescription

            // dd($prescriptions->choose_services);
            
            $existingServiceIds = json_decode($prescriptions->choose_services); 
            
          //  dd($existingServiceIds);
            
              $alreadyProcessed = [];
              $notProcessed = [];
            
           
             
             if ($existingServiceIds) {
                $alreadyProcessed = array_intersect($serviceIds, $existingServiceIds);
                $notProcessed = array_diff($serviceIds, $existingServiceIds);
            } else {
                $notProcessed = $serviceIds; // None are processed if existing IDs are null
            }
        
            // Check if there are already processed IDs
            if (!empty($alreadyProcessed)) {
                return response()->json([
                    'status' => false,
                    'message' => 'Already Done',
                    'data' => $alreadyProcessed
                ]);
            }
          
    
    
          
            
            
            
            else{
                
                
            $user=prescription::with('appointment_details','user','pet')->where('id',$prescriptions1[0]->id)->get();
            
           // dd($user[0]->aaid);
                
                  // Retrieve the validated 'service_ids' from the request
           
                
             
                
                       foreach ($serviceIds as $key => $service) {
                           
                               // dd($key+1);
                         
                             $check=subservice::where('id',$service)->get();
                                
                             // dd($check->subservicename);
                             //dd( $user[0]->aaid);
                                 try {
                                // Create clinictransaction entry
                                $add_details = clinictransaction::create([
                                    'payment_mode' => 1, 
                                    'payment_for' => $check[0]->subservicename,
                                    'amount' => $check[0]->fee,
                                    'paid_by_id' => Auth()->user()->id,
                                    'aid' => $user[0]->aaid ?? null, // Use null coalescing to prevent errors
                                    'uid' => $user[0]->user->id ?? null,
                                    'pid' => $user[0]->pet->id ?? null,
                                ]);
                    
        
                                // Create labreport entry
                                $assign_labs = labreport::create([
                                    'aid' => $user[0]->aaid ?? null, 
                                    'pid' => $user[0]->pet->id ?? null, 
                                    'uid' => $user[0]->user->id ?? null, 
                                    'ssid' =>$service, 
                                    'ssid_name' => $check[0]->subservicename, 
                                    'statusid' => 1,
        
                                ]);
        
        
                            //   $prescriptions->choose_services=
        
                            } catch (\Exception $e) {
                                // Log the error for debugging
                                \Log::error('Error creating records: ' . $e->getMessage());
                                return response()->json(['error' => $e->getMessage()], 500);
                            }
                           
                           
                           
                       }
                       
                       
                        $chage_statusp=prescription::where('id',$prescriptions1[0]->id)->update([
                            'payment_status'=>true,
                            'choose_services'=>$serviceIds,
                            //  $prescriptions->choose_services=
                        ]);

                        $chage_status=appointment::where('id',$id)->update([
                            'lab_payments'=>1
                        ]);

                
                
              
                  // dd($check);
             
                 return response()->json([
                'status'=>true,
                'message' => 'Transaction details saved successfully!'
            ]);
                
                
            }
            




  
   
   
   
}


public function getServices(Request $request, $id)
{
    // Define validation rules
    $rules = [
        'service_ids' => 'required|array|min:1', // Ensure it's an array with at least one ID
        'service_ids.*' => 'integer',
        'payment_type'=>'required|in:UPI,CASH,CARD',
        'transactionid'=>'required|string',
    ];

    // Define custom messages
    $messages = [
        'service_ids.required' => 'service_ids Required',
        'service_ids.array' => 'service_ids Required in array',
        'service_ids.min' => 'service_ids Min One',
        'payment_type.in' => 'The selected payment type must be one of the following: UPI, CASH, or CARD.',
    ];

    // Perform validation
    $validator = \Validator::make($request->all(), $rules, $messages);

    if ($validator->fails()) {
        // Handle failed validation
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ])
        );
    }

    $serviceIds = $validator->validated()['service_ids'];

    // Get the prescription
    $prescriptions1 = prescription::where('aaid', $id)->get(); 
    $prescriptions = prescription::find($prescriptions1[0]->id);

    $existingServiceIds = json_decode($prescriptions->choose_services); 
    
    $alreadyProcessed = [];
    $notProcessed = [];

    // If there are existing service IDs
    if ($existingServiceIds) {
        $alreadyProcessed = array_intersect($serviceIds, $existingServiceIds);
        $notProcessed = array_diff($serviceIds, $existingServiceIds);
    } else {
        $notProcessed = $serviceIds; // None are processed if existing IDs are null
    }

    // Check if there are already processed IDs
    if (!empty($alreadyProcessed)) {
        return response()->json([
            'status' => false,
            'message' => 'Already Done',
            'data' => $alreadyProcessed
        ]);
    }
    
    // Initialize the new array to store service IDs that are to be processed
    $newServiceArray = [];

    // Add the service IDs to the new array
    if (!empty($notProcessed)) {
        $newServiceArray = array_merge($newServiceArray, $notProcessed);
    }

    // Continue processing the newServiceArray if required
    
    $uniq_id=$uniqueId = time() . random_int(1000000000, 9999999999);
    foreach ($newServiceArray as $service) {
        $check = subservice::where('id', $service)->get();

        try {
            // Create clinictransaction entry
            $add_details = clinictransaction::create([
                'payment_mode' => 1, 
                'transactionid' => $request->transactionid, 
                'payment_type' => $request->payment_type, 
                'payment_for' => $check[0]->subservicename,
                'amount' => $check[0]->fee,
                'paid_by_id' => Auth()->user()->id,
                'aid' => $prescriptions1[0]->aaid ?? null, // Use null coalescing to prevent errors
                'uid' => $prescriptions1[0]->user->id ?? null,
                'pid' => $prescriptions1[0]->pet->id ?? null,
                'uni_transaction_id'=>$uniq_id
            ]);

            // Create labreport entry
            $assign_labs = labreport::create([
                'aid' => $prescriptions1[0]->aaid ?? null, 
                'pid' => $prescriptions1[0]->pet->id ?? null, 
                'uid' => $prescriptions1[0]->user->id ?? null, 
                'ssid' => $service, 
                'ssid_name' => $check[0]->subservicename, 
                'statusid' => 1,
            ]);

        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error creating records: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    // Update prescription and appointment statuses
    $chage_statusp = prescription::where('id', $prescriptions1[0]->id)->update([
        'payment_status' => true,
        'choose_services' => json_encode(array_merge($existingServiceIds ?? [], $newServiceArray)),
       // 'choose_services' => json_encode(array_merge($existingServiceIds, $newServiceArray)),
    ]);

    $chage_status = appointment::where('id', $id)->update([
        'lab_payments' => 1
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Transaction details saved successfully!',
    ]);
}


        
        

        public function show_prescription_bkp($id){

            
           


            $prescriptions1 = prescription::where('aaid',$id)->get(); // Get the prescription

            // dd($prescriptions[0]->id);
            $prescriptions = prescription::find($prescriptions1[0]->id); // Get the prescription

          //  dd($prescriptions);
            $allSubservices = $prescriptions->getAllSubservicesWithFees();
            
          // dd($allSubservices['subserviceData']);
            

            $subservices_inhouse = $prescriptions->relatedInhouseSubservices();  // Call the method directly
            $subservices_grooming = $prescriptions->relatedgroomingSubservices();  // Call the method directly
            $subservices_general_services = $prescriptions->relatedservicesSubservices();  // Call the method directly
            $subservices_lab = $prescriptions->relatedlabSubservices();  // Call the method directly

            $user=prescription::with('appointment_details','user','pet','dr_user')->where('id',$prescriptions1[0]->id)->first();
            // dd($user);

            $reports=labreport::where('aid',$id)->get();
            // dd($reports);


          

        
            // $get_preceiptions=prescription::with('pet','user','dr_user','appointment_details','relatedSubservices')->where('id',$id)->get();
            return response()->json([
                'status'=>true,
               
                'prescriptions'=>$user,
                'inhouse'=>$subservices_inhouse,
                'grooming'=>$subservices_grooming,
                'general_services'=>$subservices_general_services,
                'Lab_services'=>$subservices_lab,
                'all'=>$allSubservices,
                'reports'=>$reports,
               
            ]);
        }
        
        
        
        
        public function show_prescription_bk11($id){
            
            $prescription = Prescription::with(['appointment_details', 'user', 'pet', 'dr_user', 'medicines'])
    ->where('aaid', $id)
    ->first();

if (!$prescription) {
    return response()->json([
        'status' => false,
        'message' => 'Invalid ID',
    ]);
}

// Process `choose_services` and calculate fees as before
$existingServiceIds = json_decode($prescription->choose_services) ?: [];
$allSubservices = $prescription->getAllSubservicesWithFees();
$subserviceData = $allSubservices['subserviceData']->toArray();

$sum_true = 0;
$sum_false = 0;
foreach ($subserviceData as &$item) {
    $item['status'] = in_array($item['id'], $existingServiceIds) ? false : true;
    if ($item['status']) {
        $sum_true += $item['fee'];
    } else {
        $sum_false += $item['fee'];
    }
}
$pendingstatus = $sum_false > 0 ? 1 : 0;

// Fetch medicines for the prescription
$medicines = $prescription->medicines;

// Format and return response
return response()->json([
    'status' => true,
    'prescription' => $prescription,
    // 'medicines' => $medicines,
    'inhouse' => $prescription->relatedInhouseSubservices(),
    'grooming' => $prescription->relatedgroomingSubservices(),
    'general_services' => $prescription->relatedservicesSubservices(),
    'Lab_services' => $prescription->relatedlabSubservices(),
    'subserviceData' => $subserviceData,
    'reports' => labreport::where('aid', $id)->get(),
    'need_to_pay' => $sum_true,
    'already_paid' => $sum_false,
    'pendingstatus' => $pendingstatus,
    'total_amount' => $sum_true + $sum_false,
]);

        }
        
        
        
        
             public function show_prescription_bk12($id)
{
 
 

 

    
   
    $prescriptions1 = prescription::where('aaid', $id)->get();
    
   // dd(count($prescriptions1));
    
    if(count($prescriptions1)<=0){
         return response()->json([
        'status' => false,
        'message'=>'invalid id'
       
    ]);
    }
    
    
    $prescriptions = prescription::find($prescriptions1[0]->id);
    $allSubservices = $prescriptions->getAllSubservicesWithFees();

    $user = prescription::with('appointment_details', 'user', 'pet', 'dr_user')
        ->where('id', $prescriptions1[0]->id)
        ->first();

    $existingServiceIds = json_decode($user->choose_services);
    $choose_services = $existingServiceIds ?: []; // Fallback to empty array if null
   //dd($choose_services);
    $subserviceData = $allSubservices['subserviceData'];

//dd($subserviceData);
    // Debug: Log or dump the data before processing
    // dd($choose_services, $subserviceData);

    // Add status based on `choose_services`
    
    $subserviceData = $subserviceData->toArray();
    
    $sum_true = 0;
$sum_false = 0;
$pendingstatus = 0;
    
    foreach ($subserviceData as &$item) {
        
       
               
 
  
       $item['status'] = in_array($item['id'], $choose_services) ? false : true;
       
       
       if ($item['status'] === true) {
        $sum_true += $item['fee']; // Sum for status true
    } else {
        $sum_false += $item['fee']; // Sum for status false
    }
       
       
    }
    //already_paid
    
    if($sum_false>0){
        $pendingstatus=1;
    }else if($sum_false<=0){
        $pendingstatus=0;
    }
 
    // dd($subserviceData);
     
     function updateSubserviceStatus($subservices, $choose_services) {
            foreach ($subservices as &$subservice) {
              //  dd($subservice['id']);
                $subservice['status'] = in_array($subservice['id'], $choose_services) ? false : true;
            }
            
            return $subservices;
        }


   
    $subservices_inhouse = updateSubserviceStatus($prescriptions->relatedInhouseSubservices(), $choose_services);
    $subservices_grooming = updateSubserviceStatus($prescriptions->relatedgroomingSubservices(), $choose_services);
    $subservices_general_services = updateSubserviceStatus($prescriptions->relatedservicesSubservices(), $choose_services);
    $subservices_lab = updateSubserviceStatus($prescriptions->relatedlabSubservices(), $choose_services);





    // $reports = labreport::where('aid', $id)->get();
    
    // $reportsData = $reports->map(function($report) {
    // $report->name = $report->name; // Add report name (or any other relevant field here)
    // return $report;
    
    $reports = labreport::where('aid', $id)
    ->join('subservices', 'labreports.ssid', '=', 'subservices.id') // Assuming your 'services' table has 'id' and 'name' columns
    ->select('labreports.*', 'subservices.subservicename as service_name') // Selecting all columns from labreports and service name
    ->get();

// Add the service name to the reports data (if not already included in the above query)
        $reportsData = $reports->map(function($report) {
            $report->name = $report->service_name; // Add service name to the report
            return $report;
        });
    

     $medicans=medicanprecreprion::where('pid',$prescriptions1[0]->id)->get();

    return response()->json([
        'status' => true,
        'prescriptions' => $user,
        'inhouse' => $subservices_inhouse,
        'grooming' => $subservices_grooming,
        'general_services' => $subservices_general_services,
        'Lab_services' => $subservices_lab,
        'all' => $allSubservices,
        'subserviceData' => $subserviceData,
        'reports' => $reports,
        'medicaines' => $medicans,
        'need_to_pay' => $sum_true,
         'already_paid' => $sum_false,
         'pendingstatus' => $pendingstatus,
        // '$sum_true' => $sum_true,
        // '$sum_false' => $sum_false,
        'total_amount' =>  $sum_true+$sum_false,
    ]);
}
        
        
        
        
        
       public function show_prescription_bk1($id)
{
    
   
    $prescriptions1 = prescription::where('aaid', $id)->get();
    
   // dd(count($prescriptions1));
    
    if(count($prescriptions1)<=0){
         return response()->json([
        'status' => false,
        'message'=>'invalid id'
       
    ]);
    }
    
    
    $prescriptions = prescription::find($prescriptions1[0]->id);
    $allSubservices = $prescriptions->getAllSubservicesWithFees();

    $user = prescription::with('appointment_details', 'user', 'pet', 'dr_user')
        ->where('id', $prescriptions1[0]->id)
        ->first();

    $existingServiceIds = json_decode($user->choose_services);
    $choose_services = $existingServiceIds ?: []; // Fallback to empty array if null
   //dd($choose_services);
    $subserviceData = $allSubservices['subserviceData'];

//dd($subserviceData);
    // Debug: Log or dump the data before processing
    // dd($choose_services, $subserviceData);

    // Add status based on `choose_services`
    
    $subserviceData = $subserviceData->toArray();
    
    $sum_true = 0;
$sum_false = 0;
$pendingstatus = 0;
    
    foreach ($subserviceData as &$item) {
        
       
               
 
  
       $item['status'] = in_array($item['id'], $choose_services) ? false : true;
       
       
       if ($item['status'] === true) {
        $sum_true += $item['fee']; // Sum for status true
    } else {
        $sum_false += $item['fee']; // Sum for status false
    }
       
       
    }
    //already_paid
    
    if($sum_false>0){
        $pendingstatus=1;
    }else if($sum_false<=0){
        $pendingstatus=0;
    }
 
    // dd($subserviceData);
     
     function updateSubserviceStatus($subservices, $choose_services) {
            foreach ($subservices as &$subservice) {
              //  dd($subservice['id']);
                $subservice['status'] = in_array($subservice['id'], $choose_services) ? false : true;
            }
            
            return $subservices;
        }


   
    $subservices_inhouse = updateSubserviceStatus($prescriptions->relatedInhouseSubservices(), $choose_services);
    $subservices_grooming = updateSubserviceStatus($prescriptions->relatedgroomingSubservices(), $choose_services);
    $subservices_general_services = updateSubserviceStatus($prescriptions->relatedservicesSubservices(), $choose_services);
    $subservices_lab = updateSubserviceStatus($prescriptions->relatedlabSubservices(), $choose_services);





    // $reports = labreport::where('aid', $id)->get();
    
    // $reportsData = $reports->map(function($report) {
    // $report->name = $report->name; // Add report name (or any other relevant field here)
    // return $report;
    
    $reports = labreport::where('aid', $id)
    ->join('subservices', 'labreports.ssid', '=', 'subservices.id') // Assuming your 'services' table has 'id' and 'name' columns
    ->select('labreports.*', 'subservices.subservicename as service_name') // Selecting all columns from labreports and service name
    ->get();

// Add the service name to the reports data (if not already included in the above query)
        $reportsData = $reports->map(function($report) {
            $report->name = $report->service_name; // Add service name to the report
            return $report;
        });
    

    

    return response()->json([
        'status' => true,
        'prescriptions' => $user,
        'inhouse' => $subservices_inhouse,
        'grooming' => $subservices_grooming,
        'general_services' => $subservices_general_services,
        'Lab_services' => $subservices_lab,
        'all' => $allSubservices,
        'subserviceData' => $subserviceData,
        'reports' => $reports,
        'need_to_pay' => $sum_true,
         'already_paid' => $sum_false,
         'pendingstatus' => $pendingstatus,
        // '$sum_true' => $sum_true,
        // '$sum_false' => $sum_false,
        'total_amount' =>  $sum_true+$sum_false,
    ]);
}

       public function generateA4Pdf($id)
{
    
     $prescriptions = Prescription::with('user', 'pet','appointment_details','medicines','dr_user')->where('aaid', $id)->first();
        if (!$prescriptions) {
            return response()->json(['status' => false, 'message' => 'Invalid ID']);
        }
        
      //dd($prescriptions->preceiption);
      //  dd(\Carbon\Carbon::parse($prescriptions->appointment_details->dateofapp)->format('M d Y'));
           $prescriptions1 = prescription::find($prescriptions->id);
    $allSubservices = $prescriptions1->getAllSubservicesWithFees();
    
   // dd($allSubservices);

        // Fetch reports for the given ID
        $reports = LabReport::where('aid', $id)->get();
        
        // Fetch services associated with this prescription
        $choose_services = json_decode($prescriptions->choose_services, true);
        $serviceNames = []; 

        // Get service names from `choose_services` or related subservices (depending on your logic)
        if ($choose_services) {
            foreach ($choose_services as $serviceId) {
                // Assuming you have a Service model that fetches service names by ID
                $service = Service::find($serviceId); 
                if ($service) {
                    $serviceNames[] = $service->name;  // Add the service name to the list
                }
            }
        }

       // dd($prescriptions->dr_user->name);

        // Prepare data for PDF
        $data = [
            'id' => $id,
            
            'name' => $prescriptions->user->name,
            'doctor_name' => $prescriptions->dr_user->name,
            'phone' => $prescriptions->user->phone,
            'city' => $prescriptions->user->city,
            'pet_name' => $prescriptions->pet->petname,
            'pet_gender' => $prescriptions->pet->petgender,
            'pet_petbread' => $prescriptions->pet->petbread,
            'pet_category' => $prescriptions->pet->category,
            'pet_petAge' => $prescriptions->pet->petAge,
            'petDobOptions' => $prescriptions->pet->petDobOptions,
            'preceiption' => $prescriptions->preceiption,
            'appointment_data' => \Carbon\Carbon::parse($prescriptions->appointment_details->dateofapp)->format('M d Y'),
            'medicanes'=>$prescriptions->medicines,
            'reports' => $allSubservices,
           // Include service names in the data
        ];
      //dd($data);
      // dd($data['medicanes']);

//     $pdf = PDF::loadView('pdf.patient-details', $data)
//           ->setPaper('A4', 'portrait')
//           ->setOption('margin-top', 10)   // Set top margin
//           ->setOption('margin-left', 10)  // Set left margin
//           ->setOption('margin-right', 10) // Set right margin
//           ->setOption('margin-bottom', 10); // Set bottom margin

// return $pdf->stream('patient-details.pdf');



$pdf = PDF::loadView('pdf.patient-details', $data)
    ->setPaper('A4', 'portrait'); // A4 paper size in portrait orientation

// Return the PDF response
return response($pdf->output(), 200)
    ->header('Content-Type', 'application/pdf')
    ->header('Content-Disposition', 'inline; filename="patient-details.pdf"');
}



public function generateA4Pdf1($id)
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


      


        public function create_prescription(preceiptionRequest $req){   

                $get_details=appointment::where('id',$req->aid)->get();
               
              // dd($req->medicines);
                 
            
               
            
               
               
               
                //dd($req->medicines);
               
                $add_data=prescription::create([
                    'aaid'=>$req->aid,
                    'temprature'=>$req->temprature,
                    'pid'=>$get_details[0]->pet_id,
                    'uid'=>$get_details[0]->user_id,
                    'dr_id'=>$get_details[0]->dr_id,
                    // 'inhouse'=> json_encode($req->inhouse),
                    'inhouse'=> json_encode($req->inhouse),
                    'lab'=> json_encode($req->lab),
                    'services'=>json_encode($req->services),
                    'grooming'=>json_encode($req->grooming),
                    'preceiption'=> $req->preceiption,
                ]);
                
               
                $data = json_decode($req->medicines, true);
                
                foreach ($data as $item) {
                \DB::table('medicanprecreprions')->insert([
                    'Name' => $item['Name'],
                    'Type' => $item['Type'],
                    'dose' => $item['dose'],
                    'frequency' => $item['frequency'],
                    'course' => $item['course'],
                    'options' => $item['options'],
                    'pid' => $add_data->id,
                ]);
            }




                $change_status=appointment::where('id',$req->aid)->update([
                    'status'=>1,
                    'status_details'=>env('STATUS_VISITED')
                ]);

                if($add_data){
                    
                    
                    
                    
                    return response()->json([
                        'status'=>true,
                        'message'=>'Added'
                    ]);
                }else{
                    return response()->json([
                        'status'=>false,
                        'message'=>'No Added'
                    ]);
                }
            
        }


        public function add_prescription($id){
            
            
              
            // $in_house=service::with('subservicedetails')->where('id',4)->get();
            $in_house=subservice::where('service_id',4)->get();
            $grooming_services=subservice::where('service_id',1)->get();
            $lab_test=subservice::where('service_id',5)->get();
            $general_services=subservice::where('service_id',2)->get();

            $get_patient=appointment::with('pet_details')->where('id',$id)->first();
          
            $pet_id=$get_patient->pet_id;
           // dd($pet_id);
            
            
            $get_previous_history = appointment::with('pet_details')
            ->where('pet_id', $pet_id)
            ->where('id', '<>', $id) // Correct use of the "not equal to" operator
            ->get();
            
            
              $get_previous_history1 = prescription::where('pid',$pet_id)
                ->where('aaid', '<>', $id) // Correct use of the "not equal to" operator
                ->get();
            
            
         // dd($get_previous_history1);

            return response()->json([
                'status'=>true,
                'in_house'=>$in_house,
                'grooming_services'=>$grooming_services,
                'lab_test'=>$lab_test,
                'general_services'=>$general_services,
                'patient_details'=>$get_patient,
                'previous_history'=>$get_previous_history1,
                
            ]);


        }

 
        public function appointment_confirmed_patients(){
            $get_appointment_by_id=appointment::with('pet_details')->where('dr_id','!=',null)->first();
            return response()->json([
                'status'=>true,
                'data'=>$get_appointment_by_id
            ]);
        }


        public function appointment_by_id($id){

            $get_appointment_by_id=appointment::with('pet_details')->where('id',$id)->first();
            return response()->json([
                'status'=>true,
                'data'=>$get_appointment_by_id
            ]);

        }
 
 
 
  
    public function appointment_confirmed_econsulancy(Request $req)
{
    $rules = [
        'doctor_name' => 'required',
        'aid' => 'required',
        'time' => 'required|date_format:H:i:s',
    ];

    $messages = [
        'doctor_name.required' => 'Doctor ID is required.',
        'aid.required' => 'Appointment ID is required.',
        'time.required' => 'Appointment time is required.',
    ];

    $validator = \Validator::make($req->all(), $rules, $messages);

    if ($validator->fails()) {
        return response()->json([
            'status' => false,
            'message' => 'Validation errors',
            'errors' => $validator->errors()->first(),
        ], 422);
    }

    try {
        DB::beginTransaction();

        $appointment = appointment::find($req->aid);
        if (!$appointment) {
            return response()->json(['status' => false, 'message' => 'Invalid appointment ID'], 400);
        }

        $get_consultation_fee = subservice::where('id', 15)->value('fee');
            // note here this below condition not to be true 
        if ($appointment->payment == 1) {
            $create_transaction = ClinicTransaction::create([
                'payment_mode' => $appointment->payment,
                'payment_for' => 'E-Consultation',
                'amount' => $get_consultation_fee,
                'transactionid' => $req->transaction_id,
                'payment_type' => $req->payment_mode,
                'paid_by_id' => auth()->user()->id,
                'aid' => $req->aid,
                'uid' => $appointment->user_id,
                'pid' => $appointment->pet_id,
            ]);

            $appointment->update([
                'dr_id' => $req->doctor_name,
                'status_details' => env('STATUS_CONFIRMED'),
            ]);

            if ($create_transaction) {
                DB::commit();
                return response()->json([
                    'status' => true,
                    'message' => 'Appointment Confirmed & Assigned to Doctor',
                ]);
            }
        } else {
            $currentDate = Carbon::parse($appointment->dateofapp)->toDateString();
            $newTime = $req->time;
            $newDateTime = Carbon::createFromFormat('Y-m-d H:i:s', "$currentDate $newTime");

            $appointment->update([
                'dateofapp' => $newDateTime,
                'dr_id' => $req->doctor_name,
                'status_details' => env('STATUS_CONFIRMED'),
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Appointment Confirmed & Assigned to Doctor',
            ]);
        }
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error confirming appointment: ' . $e->getMessage());
        return response()->json([
            'status' => false,
            'message' => 'An error occurred: ' . $e->getMessage(),
        ], 500);
    }
}

  
 
 
 
       public function appointment_confirmed(appointmentconfirmedRequest $req){
          // dd($get_ids[0]->user_id);
          
        $uniqueId = time() . random_int(1000000000, 9999999999);

          
           //dd($uniqueId);
          
          
          try {  
            DB::beginTransaction();
            // Get payment status
            $get_payment_status = appointment::where('id', $req->aid)->first();
            if (!$get_payment_status) {
                return response()->json(['status' => false, 'message' => 'Invalid appointment ID'], 400);
            }
            // Get consultation fee
            $get_consultation_fee = subservice::where('id', 15)->value('fee');
            // Get user and pet IDs
            $get_ids = appointment::where('id', $req->aid)->first(['user_id', 'pet_id']);
            if (!$get_ids) {
                return response()->json(['status' => false, 'message' => 'Invalid user or pet IDs'], 400);
            }
            
            //dd($get_payment_status->payment);
            if ($get_payment_status->payment == 1) {
                
                // Create clinic transaction
                $create_transaction = ClinicTransaction::create([
                    'payment_mode' => $get_payment_status->payment,
                    'payment_for' => 'In_House',
                    'amount' => $get_consultation_fee,
                    'transactionid' => $req->transaction_id,
                    'payment_type' => $req->payment_mode,
                    'paid_by_id' => Auth()->user()->id,
                    'aid' => $req->aid,
                    'uid' => $get_ids->user_id,
                    'pid' => $get_ids->pet_id,
                    'uni_transaction_id'=>$uniqueId
                ]);
                
               
                //dd($create_transaction->id);
                // Assign doctor
                $assign_doctor = appointment::where('id', $req->aid)->update([
                    'dr_id' =>$req->doctor_name,
                    'status_details'=>env('STATUS_CONFIRMED')
                ]);
                // Check success
                if ($create_transaction && $assign_doctor) {
                    DB::commit();
                  
                    return response()->json([
                        'status' => true,
                        'message' => 'Appointment Confirmed & Assigned To Doctor',
                    ]);
                } else {
                    DB::rollBack();
                    
                   
                    return response()->json([
                        'status' => false,
                        'message' => 'Failed to process transaction or assign doctor',
                    ], 500);
                }
            } 
            else
            {
                // Assign doctor if payment is not done
                $assign_doctor = appointment::where('id', $req->aid)->update([
                    'dr_id' =>$req->doctor_name,
                    'status_details'=>env('STATUS_CONFIRMED')
                ]);
                DB::commit();
                
                 //$this->generateReceipt(190);
                return response()->json([
                    'status' => true,
                    'message' => 'Appointment Confirmed & Assigned To Doctor',
                ]);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
        }
        
        
       
       
       
         public function generateReceipt($id){
             
             //dd($id);

        $get_details=clinictransaction::with('user','pet')->where('id',$id)->first();
        
        $dr_details=appointment::with('doctor_details')->where('id',$get_details->aid)->get();
       // dd($get_details->payment_type);

    //  dd($get_details->aid);
           dd($get_details->payment_for);
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
       
       
       
        
        
//     public function index(Request $request)
// {
    
//     $today = Carbon::today();

//     // Common counts
//     $get_total_appointment = appointment::count();
//     $get_todays_appointment = appointment::whereDate('dateofapp', $today)->count();
//     $get_pet_details = mypet::count();

//     // Initialize the base query
//     $baseQuery = appointment::with('doctor_details', 'pet_details');

//     // Response structure
//     $response = [
//         'status' => true,
//         'total_appointment_count' => $get_total_appointment,
//         'todays_appointment_count' => $get_todays_appointment,
//         'pet_details_count' => $get_pet_details,
//     ];

//     // Filter conditions
//     if ($request->has('type')) {
//         $type = $request->query('type');

//         if ($type === 'e-consultancy') {
//             $response['appointment_details'] = $baseQuery->where('app_type', '28')->get();
//         } elseif ($type === 'in-house') {
//             $response['appointment_details'] = $baseQuery->where('app_type', '15')->get();
//         } elseif ($type === 'all') {
//             $response['appointment_details'] = $baseQuery->get();
//         }
//     } elseif ($request->has('appointmentdate')) {
//         $date = $request->query('appointmentdate');
//         $response['today_appointment_details'] = $baseQuery->whereDate('dateofapp', $date)->get();
//     } else {
//         // Default to today's appointments
//         $response['today_appointment_details'] = $baseQuery->whereDate('dateofapp', $today)->get();
//     }

//     return response()->json($response);
// }


public function index(Request $request)
{
    // $today = Carbon::today();

    // // Common counts
    // $get_total_appointment = appointment::count();
    // $get_todays_appointment = appointment::whereDate('dateofapp', $today)->count();
    // $get_pet_details = mypet::count();

    // // Initialize the base query
    // $baseQuery = appointment::with('doctor_details', 'pet_details');

    // // Response structure
    // $response = [
    //     'status' => true,
    //     'total_appointment_count' => $get_total_appointment,
    //     'todays_appointment_count' => $get_todays_appointment,
    //     'pet_details_count' => $get_pet_details,
    // ];

    // // Validate request parameters
    // $validatedData = $request->validate([
    //     'type' => 'nullable|in:e-consultancy,in-house,all',
    //     'appointmentdate' => 'nullable|date',
    // ]);

    // // Apply filters based on type and appointment date
    // if ($request->has(['type', 'appointmentdate'])) {
    //     $type = $validatedData['type'];
    //     $date = $validatedData['appointmentdate'];

    //   // $baseQuery = $baseQuery->whereDate('dateofapp', $date);
    //      $baseQuery = $baseQuery->whereDate('dateofapp', $date)->orderBy('id', 'desc');

    //     if ($type === 'e-consultancy') {
    //         $response['appointment_details'] = $baseQuery->where('app_type', '28')->orderBy('id', 'desc')->get();
    //     } elseif ($type === 'in-house') {
    //         $response['appointment_details'] = $baseQuery->where('app_type', '15')->orderBy('id', 'desc')->get();
    //     } else {
    //         $response['appointment_details'] = $baseQuery->orderBy('id', 'desc')->get();
    //     }
    // } elseif ($request->has('type')) {
    //     $type = $validatedData['type'];

    //     if ($type === 'e-consultancy') {
    //         $response['appointment_details'] = $baseQuery->where('app_type', '28')->orderBy('id', 'desc')->get();
    //     } elseif ($type === 'in-house') {
    //         $response['appointment_details'] = $baseQuery->where('app_type', '15')->orderBy('id', 'desc')->get();
    //     } else {
    //         $response['appointment_details'] = $baseQuery->orderBy('id', 'desc')->get();
    //     }
    // } elseif ($request->has('appointmentdate')) {
    //     $date = $validatedData['appointmentdate'];
    //     //$response['appointment_details'] = $baseQuery->whereDate('dateofapp', $date)->get();
    //      $response['appointment_details'] = $baseQuery->whereDate('dateofapp', $date)->orderBy('id', 'desc')->get();
    // } else {
    //     // Default to today's appointments
    //   //  $response['appointment_details'] = $baseQuery->whereDate('dateofapp', $today)->get();
    //   $response['appointment_details'] = $baseQuery->whereDate('dateofapp', $today)->orderBy('id', 'desc')->get();
    // }

    // return response()->json($response);
    
    
    
   
    
//     $today = Carbon::today();

// // Common counts
// $get_total_appointment = appointment::count();
// $get_todays_appointment = appointment::whereDate('dateofapp', $today)->count();
// $get_pet_details = mypet::count();

// // Initialize the base query
// $baseQuery = appointment::with('doctor_details', 'pet_details','user_details');

// // Response structure
// $response = [
//     'status' => true,
//     'total_appointment_count' => $get_total_appointment,
//     'todays_appointment_count' => $get_todays_appointment,
//     'pet_details_count' => $get_pet_details,
// ];

// // Validate request parameters
// $validatedData = $request->validate([
//     'type' => 'nullable|in:e-consultancy,in-house,all',
//     'appointmentdate' => 'nullable|date',
//     'id' => 'nullable|integer',
//     'pet_owner_name' => 'nullable|string',
// ]);

// // Apply filters based on type, appointment date, id, and pet_owner_name
// if ($request->has(['type', 'appointmentdate', 'id', 'pet_owner_name'])) {
//     $type = $validatedData['type'];
//     $date = $validatedData['appointmentdate'];
//     $id = $validatedData['id'];
//     $petOwnerName = $validatedData['pet_owner_name'];

//     // Apply date filter and order by id in descending order
//     $baseQuery = $baseQuery->whereDate('dateofapp', $date)->orderBy('id', 'desc');

//     // If id is provided, filter by id
//     if ($id) {
//         $baseQuery = $baseQuery->where('id', $id);
//     }

//     // If pet_owner_name is provided, filter by pet owner's name (assuming relationship to pet_details table)
//     if ($petOwnerName) {
//         $baseQuery = $baseQuery->whereHas('user_details', function ($query) use ($petOwnerName) {
//             $query->where('name', 'like', "%{$petOwnerName}%");
//         });
//     }

//     // Apply type filter if provided
//     if ($type === 'e-consultancy') {
//         $response['appointment_details'] = $baseQuery->where('app_type', '28')->get();
//     } elseif ($type === 'in-house') {
//         $response['appointment_details'] = $baseQuery->where('app_type', '15')->get();
//     } else {
//         $response['appointment_details'] = $baseQuery->get();
//     }
// } elseif ($request->has('type')) {
  
//     $type = $validatedData['type'];
    
//     $baseQuery = $baseQuery->orderBy('id', 'desc');
    
//     if ($type === 'e-consultancy') {
//         $response['appointment_details'] = $baseQuery->where('app_type', '28')->get();
//     } elseif ($type === 'in-house') {
//         $response['appointment_details'] = $baseQuery->where('app_type', '15')->get();
//     } elseif($type === 'all') {
        
//         $response['appointment_details'] = $baseQuery->get();
//     }
// } elseif ($request->has('appointmentdate')) {
//     $date = $validatedData['appointmentdate'];
//     $response['appointment_details'] = $baseQuery->whereDate('dateofapp', $date)->orderBy('id', 'desc')->get();
// } elseif ($request->has('id')) {
//     $id = $validatedData['id'];
//     $response['appointment_details'] = $baseQuery->where('id', $id)->orderBy('id', 'desc')->get();
// } elseif ($request->has('pet_owner_name')) {
//     $petOwnerName = $validatedData['pet_owner_name'];
//     $response['appointment_details'] = $baseQuery->whereHas('user_details', function ($query) use ($petOwnerName) {
//         $query->where('name', 'like', "%{$petOwnerName}%");
//     })->orderBy('id', 'desc')->get();
// } else {
//     // Default to today's appointments
//     $response['appointment_details'] = $baseQuery->whereDate('dateofapp', $today)->orderBy('id', 'desc')->get();
// }

// return response()->json($response);



 $today = Carbon::today();

    // Common counts
    $get_total_appointment = appointment::count();
    $get_todays_appointment = appointment::whereDate('dateofapp', $today)->count();
    $get_pet_details = mypet::count();

    // Initialize the base query
    $baseQuery = appointment::with('doctor_details', 'pet_details', 'user_details');

    // Response structure
    $response = [
        'status' => true,
        'total_appointment_count' => $get_total_appointment,
        'todays_appointment_count' => $get_todays_appointment,
        'pet_details_count' => $get_pet_details,
    ];

    // Validate request parameters
    $validatedData = $request->validate([
        'type' => 'nullable|in:e-consultancy,in-house,all',
        'appointmentdate' => 'nullable|date',
        'id' => 'nullable|integer',
        'pet_owner_name' => 'nullable|string',
    ]);

    // Apply filters dynamically
    if ($request->filled('appointmentdate')) {
        $baseQuery->whereDate('dateofapp', $validatedData['appointmentdate']);
    }

    if ($request->filled('id')) {
        $baseQuery->where('id', $validatedData['id']);
    }

    if ($request->filled('pet_owner_name')) {
        $baseQuery->whereHas('user_details', function ($query) use ($validatedData) {
            $query->where('name', 'like', '%' . $validatedData['pet_owner_name'] . '%');
        });
    }

    if ($request->filled('type')) {
        if ($validatedData['type'] === 'e-consultancy') {
            $baseQuery->where('app_type', '28');
        } elseif ($validatedData['type'] === 'in-house') {
            $baseQuery->where('app_type', '15');
        }
        // If type is "all", no additional filter is applied.
    }

    // Order the results
    $baseQuery->orderBy('id', 'desc');

    // Fetch the filtered appointment details
    $response['appointment_details'] = $baseQuery->get();

    // Return the response
    return response()->json($response);

    
    
    
    
    
    
    
    
    
    
    
}





        
        
        
        public function index1(Request $request){
          
            $today=Carbon::today();
           $get_total_appointment=appointment::count();
           $get_todays_appointment=appointment::whereDate('dateofapp',$today)->count();
          
           $get_todays_appointment_details=appointment::with('doctor_details','pet_details')->whereDate('dateofapp',operator: $today)->get();
          
           $get_pet_details=mypet::count();
           $get_my_pet_details=mypet::get();
        //    $petId = $request->query('petid');
        
           $dateofapointment = $request->query('appointmentdate');
           $all = $request->query('all');
           $eConsultancy = $request->query('e-Consultancy');
            $inHouse = $request->query('inHouse');
       
       
       
      if($eConsultancy){
           
            $get_appointment_details_by_all=appointment::with('doctor_details','pet_details')->where('app_type','28')->get();
            return response()->json([
              'status'=>true,
            'total_appointment_count'=>$get_total_appointment,
            'todays_appointment_count'=>$get_todays_appointment,
            'pet_details_count'=>$get_pet_details,
            'appointment_details_by_all'=>$get_appointment_details_by_all,
                // 'get_pet_details'=>$get_pet_details,
              ]);
      }
       
         if($inHouse){
           
         
            $get_appointment_details_by_all=appointment::with('doctor_details','pet_details')->where('app_type','15')->get();
            return response()->json([
              'status'=>true,
            'total_appointment_count'=>$get_total_appointment,
            'todays_appointment_count'=>$get_todays_appointment,
            'pet_details_count'=>$get_pet_details,
            'appointment_details_by_all'=>$get_appointment_details_by_all,
                // 'get_pet_details'=>$get_pet_details,
              ]);
      }
       
       
       
       
       
       
        if ($all) {
            $get_appointment_details_by_all=appointment::with('doctor_details','pet_details')->get();
            return response()->json([
              'status'=>true,
            'total_appointment_count'=>$get_total_appointment,
            'todays_appointment_count'=>$get_todays_appointment,
            'pet_details_count'=>$get_pet_details,
            'appointment_details_by_all'=>$get_appointment_details_by_all,
                // 'get_pet_details'=>$get_pet_details,
               ]);
            // Replace with your logic
        }
        if ($dateofapointment) {
            $get_appointment_details_by_date=appointment::with('doctor_details','pet_details')->whereDate('dateofapp',operator: $dateofapointment)->get();
            return response()->json([
              'status'=>true,
            'total_appointment_count'=>$get_total_appointment,
            'todays_appointment_count'=>$get_todays_appointment,
            'pet_details_count'=>$get_pet_details,
            'today_appointment_details'=>$get_appointment_details_by_date,
                // 'get_pet_details'=>$get_pet_details,
               ]);
            // Replace with your logic
        }
           return response()->json([
            'status'=>true,
            'total_appointment_count'=>$get_total_appointment,
            'todays_appointment_count'=>$get_todays_appointment,
            'pet_details_count'=>$get_pet_details,
            'today_appointment_details'=>$get_todays_appointment_details,
            // 'get_my_pet_details'=>$get_my_pet_details
           ]);
        }
        
        
        
        //
        public function doctor_all(Request $request){
        //     $user_id=Auth()->user()->id;
          
        //     $today=Carbon::today();
        //   $get_total_appointment=appointment::where('dr_id', $user_id)->count();
        //   $get_todays_appointment=appointment::whereDate('dateofapp',$today)->where('dr_id', $user_id)->count();
        //   $get_todays_appointment_details=appointment::with('doctor_details','pet_details','user_details')->whereDate('dateofapp',operator: $today)->where('dr_id', $user_id)->get();
         
        //   // dd($get_todays_appointment_details);
         
        //   $get_pet_details=mypet::count();
        //   $get_my_pet_details=mypet::get();
        // //    $petId = $request->query('petid');
        //   $dateofapointment = $request->query('appointmentdate');

        //   if ($dateofapointment) {
           
        //     $get_appointment_details_by_date=appointment::with('doctor_details','pet_details','user_details')->whereDate('dateofapp',operator: $dateofapointment)->where('dr_id', $user_id)->get();
        //     return response()->json([
        //       'status'=>true,
        //     'total_appointment_count'=>$get_total_appointment,
        //     'todays_appointment_count'=>$get_todays_appointment,
        //     'pet_details_count'=>$get_pet_details,
        //     'today_appointment_details'=>$get_appointment_details_by_date,
        //         // 'get_pet_details'=>$get_pet_details,
        //       ]);
          
        // }



        //   //dd($dateofapointment);
        //     $get_appointment_details_by_all=appointment::with('doctor_details','pet_details','user_details')->where('dr_id', $user_id)->get();
        //     return response()->json([
        //       'status'=>true,
        //     'total_appointment_count'=>$get_total_appointment,
        //     'todays_appointment_count'=>$get_todays_appointment,
        //     'pet_details_count'=>$get_pet_details,
        //     'today_appointment_details'=>$get_appointment_details_by_all,
        //         // 'get_pet_details'=>$get_pet_details,
        //       ]);
        //     // Replace with your logic
        
        
        $user_id = Auth()->user()->id;
$today = Carbon::today();

$get_total_appointment = appointment::where('dr_id', $user_id)->count();
$get_todays_appointment = appointment::whereDate('dateofapp', $today)
    ->where('dr_id', $user_id)
    ->count();

$get_pet_details = mypet::count();

// Fetch appointments for today, ordered by ID (latest first)
$get_todays_appointment_details = appointment::with('doctor_details', 'pet_details', 'user_details')
    ->whereDate('dateofapp', $today)
    ->where('dr_id', $user_id)
    ->orderBy('id', 'desc') // Order by ID in descending order
    ->get();

// Check if the request contains an appointment date filter
$dateofappointment = $request->query('appointmentdate');

if ($dateofappointment) {
    $get_appointment_details_by_date = appointment::with('doctor_details', 'pet_details', 'user_details')
        ->whereDate('dateofapp', $dateofappointment)
        ->where('dr_id', $user_id)
        ->orderBy('id', 'desc') // Order by ID in descending order
        ->get();

    return response()->json([
        'status' => true,
        'total_appointment_count' => $get_total_appointment,
        'todays_appointment_count' => $get_todays_appointment,
        'pet_details_count' => $get_pet_details,
        'today_appointment_details' => $get_appointment_details_by_date,
    ]);
}

// Fetch all appointments, ordered by ID (latest first)
$get_appointment_details_by_all = appointment::with('doctor_details', 'pet_details', 'user_details')
    ->where('dr_id', $user_id)
    ->orderBy('id', 'desc') // Order by ID in descending order
    ->get();

return response()->json([
    'status' => true,
    'total_appointment_count' => $get_total_appointment,
    'todays_appointment_count' => $get_todays_appointment,
    'pet_details_count' => $get_pet_details,
    'today_appointment_details' => $get_appointment_details_by_all,
]);

        
        
        

       
        }
        
         public function all(Request $request){
           
           $today = Carbon::today();
$get_total_appointment = appointment::count();
$get_todays_appointment = appointment::whereDate('dateofapp', $today)->count();
$get_todays_appointment_details = appointment::with('doctor_details', 'pet_details','user_details')
    ->whereDate('dateofapp', $today)
    ->orderBy('id', 'desc')
    ->get();
$get_pet_details = mypet::count();
$get_my_pet_details = mypet::get();

$dateofapointment = $request->query('appointmentdate');
$id = $request->query('id');
$ownerName = $request->query('ownerName');

if ($dateofapointment) {
    $get_appointment_details_by_date = appointment::with('doctor_details', 'pet_details','user_details')
        ->whereDate('dateofapp', $dateofapointment)
        ->orderBy('id', 'desc') // Order by ID in descending order
        ->get();

    return response()->json([
        'status' => true,
        'total_appointment_count' => $get_total_appointment,
        'todays_appointment_count' => $get_todays_appointment,
        'pet_details_count' => $get_pet_details,
        'today_appointment_details' => $get_appointment_details_by_date,
    ]);
}

if ($ownerName) {
    $get_appointment_details_by_owner = appointment::with('doctor_details', 'pet_details', 'user_details')
        ->whereHas('user_details', function ($query) use ($ownerName) {
            $query->where('name', 'like', '%' . $ownerName . '%'); // Adjust 'name' to the column storing owner names
        })
        ->orderBy('id', 'desc') // Order by ID in descending order
        ->get();

    return response()->json([
        'status' => true,
        'total_appointment_count' => $get_total_appointment,
        'todays_appointment_count' => $get_todays_appointment,
        'pet_details_count' => $get_pet_details,
        'today_appointment_details' => $get_appointment_details_by_owner,
    ]);
}


if ($id) {
    $get_appointment_details_by_date = appointment::with('doctor_details', 'pet_details','user_details')
        ->where('id', $id)
        ->orderBy('id', 'desc') // Order by ID in descending order
        ->get();

    return response()->json([
        'status' => true,
        'total_appointment_count' => $get_total_appointment,
        'todays_appointment_count' => $get_todays_appointment,
        'pet_details_count' => $get_pet_details,
        'today_appointment_details' => $get_appointment_details_by_date,
    ]);
}

$get_appointment_details_by_all = appointment::with('doctor_details', 'pet_details','user_details')
    ->orderBy('id', 'desc') // Order by ID in descending order
    ->get();

return response()->json([
    'status' => true,
    'total_appointment_count' => $get_total_appointment,
    'todays_appointment_count' => $get_todays_appointment,
    'pet_details_count' => $get_pet_details,
    'today_appointment_details' => $get_appointment_details_by_all,
]);

           
            // Replace with your logic
       
        }
        
        
        
        public function assign_to_doctor(){
            $get_appointment=appointment::where('dr_id',null)->get();
            $get_doctor_details=user::where('user_type',2)->get();
            return response()->json([
                'status'=>true,
                'data'=>$get_appointment,
                'doctors'=>$get_doctor_details
            ]);
        }
}
