<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\addDoctorRequest;
use Str;
use DB;
use Hash;
use Mail;
use App\Mail\SendPassword;
use App\Jobs\SendEmailJobForRegistrationPassword;
use App\Http\CustomHelper;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use App\Models\appointment;
use App\Models\mypet;
use App\Models\subservice;
use Illuminate\Support\Facades\Auth;

class DoctorController extends Controller
{
    //
    
//         public function index(Request $request)
// {
    
    
//   $user_type=Auth()->user()->user_type;
    
//     $user_id=Auth()->user()->id;
    
//   // dd($user_id);
    
//     if($user_type!=2){
//         return response()->json([
//                 'status'=>false,
//                 'message'=>'Login Credentails Not Belongs to Doctor...!',
//             ]);
//     }
    
//     $today = Carbon::today();


//     // Common counts
//     $get_total_appointment = appointment::where('dr_id',$user_id)->count();
//     $get_todays_appointment = appointment::whereDate('dateofapp', $today)->where('dr_id',$user_id)->count();
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
//             $response['appointment_details'] = $baseQuery->where('app_type', '28')->where('dr_id',$user_id)->get();
//         } elseif ($type === 'in-house') {
//             $response['appointment_details'] = $baseQuery->where('app_type', '15')->where('dr_id',$user_id)->get();
//         } elseif ($type === 'all') {
           
//              $response['appointment_details'] = $baseQuery->where('dr_id',$user_id)->get();
//         }
//     } elseif ($request->has('appointmentdate')) {
//         $date = $request->query('appointmentdate');
//         $response['today_appointment_details'] = $baseQuery->whereDate('dateofapp', $date)->where('dr_id',$user_id)->get();
//     } else {
//         // Default to today's appointments
//         $response['today_appointment_details'] = $baseQuery->whereDate('dateofapp', $today)->where('dr_id',$user_id)->get();
//     }

//     return response()->json($response);
// }


public function index(Request $request)
{
    // $user_type = Auth()->user()->user_type;
    // $user_id = Auth()->user()->id;

    // if ($user_type != 2) {
    //     return response()->json([
    //         'status' => false,
    //         'message' => 'Login Credentials Not Belong to Doctor...!',
    //     ]);
    // }

    // $today = Carbon::today();

    // // Common counts
    // $get_total_appointment = appointment::where('dr_id', $user_id)->count();
    // $get_completed_appointment = appointment::where('dr_id', $user_id)->where('status',1)->orWhere('status',2)->count();
    // $get_todays_appointment = appointment::whereDate('dateofapp', $today)->where('dr_id', $user_id)->count();
    // $get_pet_details = mypet::count();

    // // Initialize the base query
    // $baseQuery = appointment::with('doctor_details', 'pet_details','user_details')->where('dr_id', $user_id);

    // // Response structure
    // $response = [
    //     'status' => true,
    //     'total_appointment_count' => $get_total_appointment,
    //     'completed_appointment' => $get_completed_appointment,
    //     'todays_appointment_count' => $get_todays_appointment,
    //     'pet_details_count' => $get_pet_details,
    // ];

    // // Validate request parameters
    // $validatedData = $request->validate([
    //     'type' => 'nullable|in:e-consultancy,in-house,all',
    //     'appointmentdate' => 'nullable|date',
    // ]);

    // // Apply filters for type and appointment date
    // if ($request->has(['type', 'appointmentdate'])) {
    //     $type = $validatedData['type'];
    //     $date = $validatedData['appointmentdate'];

    //     $baseQuery = $baseQuery->whereDate('dateofapp', $date);

    //     if ($type === 'e-consultancy') {
    //         $response['appointment_details'] = $baseQuery->where('app_type', '28')->get();
    //     } elseif ($type === 'in-house') {
    //         $response['appointment_details'] = $baseQuery->where('app_type', '15')->get();
    //     } else {
    //         $response['appointment_details'] = $baseQuery->get();
    //     }
    // } elseif ($request->has('type')) {
    //     $type = $validatedData['type'];

    //     if ($type === 'e-consultancy') {
    //         $response['appointment_details'] = $baseQuery->where('app_type', '28')->get();
    //     } elseif ($type === 'in-house') {
    //         $response['appointment_details'] = $baseQuery->where('app_type', '15')->get();
    //     } else {
    //         $response['appointment_details'] = $baseQuery->get();
    //     }
    // } elseif ($request->has('appointmentdate')) {
    //     $date = $validatedData['appointmentdate'];
    //     $response['appointment_details'] = $baseQuery->whereDate('dateofapp', $date)->get();
    // } else {
    //     // Default to today's appointments
    //     $response['appointment_details'] = $baseQuery->whereDate('dateofapp', $today)->get();
    // }

    // return response()->json($response);
    


    $user = Auth::user();

    // Check if the user is a doctor
    if (!$user || $user->user_type != 2) {
        return response()->json([
            'status' => false,
            'message' => 'Login Credentials Not Belong to Doctor...!',
        ]);
    }

    $today = Carbon::today();

    // Common counts
    $get_total_appointment = appointment::where('dr_id', $user->id)->count();
    $get_completed_appointment = appointment::where('dr_id', $user->id)
        ->whereIn('status', [1, 2])
        ->count();
    $get_todays_appointment = appointment::whereDate('dateofapp', $today)
        ->where('dr_id', $user->id)
        ->count();
    $get_pet_details = mypet::count();

    // Initialize base query with sorting
    $baseQuery = appointment::with(['doctor_details', 'pet_details', 'user_details'])
        ->where('dr_id', $user->id)
        ->orderBy('id', 'desc'); // Sorting by ID in descending order

    // Response structure
    $response = [
        'status' => true,
        'total_appointment_count' => $get_total_appointment,
        'completed_appointment' => $get_completed_appointment,
        'todays_appointment_count' => $get_todays_appointment,
        'pet_details_count' => $get_pet_details,
    ];

    // Validate request parameters
    $validatedData = $request->validate([
        'type' => 'nullable|in:e-consultancy,in-house,all',
        'appointmentdate' => 'nullable|date',
    ]);

    // Apply filters if parameters are provided
    if ($request->filled('appointmentdate')) {
        $baseQuery->whereDate('dateofapp', $validatedData['appointmentdate']);
    }

    if ($request->filled('type') && $validatedData['type'] !== 'all') {
        $appType = $validatedData['type'] === 'e-consultancy' ? '28' : '15';
        $baseQuery->where('app_type', $appType);
    }

    // Fetch filtered appointments
    $response['appointment_details'] = $baseQuery->get();

    return response()->json($response);



    
}
    
    
    
    
    
    
    public function delete_staff($id){
        
        
        $check_user_type=User::where('id',$id)->value('user_type');
        $status=User::where('id',$id)->value('active_status');
        
        
        // dd($check_user_type);
        // dd($check_user_type[0]->active_status);
        
        
        $values = ['2', '3', '4'];  //2 for doc;3 for :receip ;4 for lab

         

        if (in_array($check_user_type, $values)) {
                if($status==1){
                            
                      $change=User::where('id',$id)->update([
                        'active_status'=>0
                    
                             ]);
                
                    if($change==1){
                         return response()->json([
                            'status'=>true,
                            'message'=>'Inactivated User'
                            ]);
                    }  
                        
                    
                }else if($status==0){
                    
                     $change=User::where('id',$id)->update([
                        'active_status'=>1
                    
                             ]);
                
                    if($change==1){
                         return response()->json([
                            'status'=>true,
                            'message'=>'Activeted User'
                            ]);
                    }  
                        
                    
                    
                    
                }
                    
                
                
                    
                    
            
        }else{
            
            return response()->json([
                'status'=>false,
                'message'=>'Invalid user type'
                ]);
        }
        
            
    }
    
    
    
    
    public function generateAlphanumericPassword($length = 8) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
        $password = '';
    
        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }
    
        return $password;
    }
    
  

    public function add_staff(addDoctorRequest $req){

        $password_generate = $this->generateAlphanumericPassword();

        //dd($password_generate);

        DB::beginTransaction();

        try {


        $create_staff=User::create([
            'name'=>$req->staff_name,
            'email'=>$req->email,
            'phone'=>$req->phone,
            'username' => Str::slug($req->staff_name) . rand(1, 500),
            'user_type' => $req->role, // for doctor
            // 'user_type' => 2, // for doctor
            'password' => Hash::make($password_generate),
            'designation'=>$req->designation

        ]);

        $roleId = 2; // Replace with the actual role ID for "customer"

        DB::table('model_has_roles')->insert([
            'role_id' => $roleId,
            'model_type' => 'App\Models\User', // Ensure this matches your user model's namespace
            'model_id' => $create_staff->id,
        ]);

   
        $details=['name'=>$req->staff_name , 'mail'=>$req->email ,'password'=>$password_generate];  
        
        SendEmailJobForRegistrationPassword::dispatch($details,$req->email);   
        
        

        // Mail::to($req->email)->send(new SendPassword($details));
        DB::commit();
         CustomHelper::create_staff($req->phone,$req->staff_name,$req->email,$password_generate); 
        return response()->json([
            
            
            'status'=>true,
            'message'=>'Employee Details added, please check Registered Email for password',

        ]);

    }catch (\Exception $e) {
        // Rollback the transaction if there was an error
        DB::rollBack();
    
        // Optionally, log or handle the exception
        return response()->json(['error' => 'Failed to create staff member and assign role: ' . $e->getMessage()], 500);
    }





       

     



    }

    public function get_staff(){

        $user_type=Auth()->user()->user_type;
       // dd($user_type);


       if($user_type==0 || $user_type==3){
        
        $data = User::whereIn('user_type', [2, 3, 4])->get();
        return response()->json([
            'status'=>true,
            'data'=>$data,
        ]);
       } else{
        return response()->json([
            'status'=>false,
            'message'=>'Unauthorized',
        ],401);
       }

      


    }
    

public function getStaff(Request $request)
{
    $user_type = Auth()->user()->user_type;
    
    $get_amount=subservice::where('id',15)->value('fee');
    //dd($get_amount);

    if ($user_type == 0 || $user_type == 3) {
        // Map type values to user_type
        $typeMap = [
            'doctor' => 2,
            'receptionist' => 3,
            'lab_technician' => 4,
        ];

        $type = $request->query('type'); // Get the type from the query string

        if ($type && array_key_exists($type, $typeMap)) {
            // Filter by specific type if provided and valid
            $data = User::where('user_type', $typeMap[$type])->where('active_status',1)->get();
        } else {
            // Return all staff if no valid type is provided
            $data = User::whereIn('user_type', array_values($typeMap))->where('active_status',1)->get();
        }

        return response()->json([
            'status' => true,
            'data' => $data,
            'fee'=>$get_amount
            
        ]);
    } else {
        return response()->json([
            'status' => false,
            'message' => 'Unauthorized',
        ], 401);
    }
}

   
   
   public function update_staff_by_id(Request $request,$id){
       
       
    $rules = [
        'staff_name' => 'required',
        'designation' => 'required',
        'role' => 'required|numeric|in:2,3,4',
        'email' => 'required|email|max:255|unique:users,email',
        'phone' => 'required|numeric|digits:10|unique:users,phone',
    ];

    // Custom attribute names
    $attributes = [
        'staff_name' => 'Employee Name',
        'designation' => 'Employee Designation',
        'role' => 'Employee Role',
    ];

    // Custom error messages
    $messages = [
        'email.unique' => 'Email Already Registered',
    ];

    // Perform validation
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);

    // Handle validation failure
    if ($validator->fails()) {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422)
        );
    }

    // Proceed with storing the data if validation passes
    // Example logic:
    $staff = User::create([
        'name' => $request->staff_name,
        'designation' => $request->designation,
        'user_type' => $request->role,
        'email' => $request->email,
        'phone' => $request->phone,
    ]);

    return response()->json([
        'status' => true,
        'message' => 'Staff created successfully',
        'data' => $staff,
    ]);
       
       
       
       
        // 'staff_name'=>'required',
        //     'designation'=>'required',
        //     'role'=>'required|numeric|in:2,3,4',
        //      'email' => 'required|email|max:255|unique:users,email',
        //     'phone'=>'required|numeric|digits:10|unique:users,phone',
       
       
       dd($id);
       
   }
   
   
   public function storeOrUpdateStaff(Request $request, $id = null)
{
    // Check if this is an update operation
    $isUpdate = $id !== null;

    // Validation rules
    $rules = [
        'staff_name' => 'required',
        'designation' => 'required',
        'role' => 'required|numeric|in:2,3,4',
        'password' => 'nullable',
        'email' => [
            'required',
            'email',
            'max:255',
            Rule::unique('users', 'email')->ignore($id),
        ],
        'phone' => [
            'required',
            'numeric',
            'digits:10',
            Rule::unique('users', 'phone')->ignore($id),
        ],
    ];

    // Custom attribute names
    $attributes = [
        'staff_name' => 'Employee Name',
        'designation' => 'Employee Designation',
        'role' => 'Employee Role',
    ];

    // Custom error messages
    $messages = [
        'email.unique' => 'Email Already Registered',
        'phone.unique' => 'Phone Number Already Registered',
    ];

    // Perform validation
    $validator = Validator::make($request->all(), $rules, $messages, $attributes);

    // Handle validation failure
    if ($validator->fails()) {
        throw new HttpResponseException(
            response()->json([
                'status' => false,
                'message' => 'Validation errors',
                'errors' => $validator->errors(),
            ], 422)
        );
    }


    //dd($request->password);
    
    if($request->password==null){
        
        if ($isUpdate) {
       $staff = User::findOrFail($id);
            $staff->update([
                'name' => $request->staff_name,
                'designation' => $request->designation,
                'user_type' => $request->role, // Assuming role maps to user_type
                'email' => $request->email,
                'phone' => $request->phone,
                //'password' => Hash::make($request->password),
                //'phone' => $request->password,
            ]);
            $message = 'Staff updated successfully';
      
    }
    
   

    return response()->json([
        'status' => true,
        'message' => $message,
        'data' => $staff,
    ]);
        
        
        
       
    }else{
        
       if ($isUpdate) {
       $staff = User::findOrFail($id);
            $staff->update([
                'name' => $request->staff_name,
                'designation' => $request->designation,
                'user_type' => $request->role, // Assuming role maps to user_type
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
                //'phone' => $request->password,
            ]);
            $message = 'Staff updated successfully';
      
    }
    
   

    return response()->json([
        'status' => true,
        'message' => $message,
        'data' => $staff,
    ]);
    }
    
   // dd("stop");

    // Proceed with storing or updating the data
    
}

   
   
   
   
   
   
    

    public function get_staff_by_id($id){

        $user = User::where('id', $id)->first();

        if ($user) {
            // Map user_type to role name
            $userRoles = [
               
                0 => 'Admin',
                1 => 'Customer',
                2 => 'Doctor',
                3 => 'Receiptionist',
                4 => 'Lab',
                5 => 'POS OPERATOR',


            ];
    
            $user->role_name = $userRoles[$user->user_type] ?? 'Unknown';
    
            return response()->json([
                'status' => true,
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }



        if ($user) {
            return response()->json([
                'status' => true,
                'data' => $user
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'User not found'
            ], 404);
        }

        // if(count($user)>0){
        //     return response()->json([
        //         'status'=>false,
        //         'data'=>$user
        //     ],200);

        // }
        
        // else{
        //     return response()->json([
        //         'status'=>false,
        //         'message'=>'No Data Available'
        //     ],401);
        // }
       
       }         

}
