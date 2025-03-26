<?php

namespace App\Http\Controllers;

use GrahamCampbell\ResultType\Success;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Str;
use App\Http\Requests\UserRequest;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\sendResetRequest;
use App\Http\Requests\resetRequest;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Password;
use Mail;
use App\Mail\SendOTP;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Jobs\SendEmailJob;
use App\Http\CustomHelper;
use App\Mail\SendPassword;
use App\Mail\SendEmailOtp;

use App\Services\Msg91Service;
use Illuminate\Http\Exceptions\HttpResponseException;



class AuthController extends Controller
{
    //
      protected $msg91Service;
      
      
      // for testing only Asif
        public function Quatation(){
        
        $data = [
    [
        "quote" => "The only way to do great work is to love what you do.",
        // "author" => "Steve Jobs"
        "author" => "Visweswara Reddy"
    ],
    [
        "quote" => "Success doesn’t come from what you do occasionally, it comes from what you do consistently.",
        "author" => "Rani"
    ],
    [
        "quote" => "The future belongs to those who believe in the beauty of their dreams.",
        "author" => "Naga Gopi"
    ],
    [
        "quote" => "It always seems impossible until it’s done.",
        "author" => "Mahesh"
    ],
    [
        "quote" => "Your limitation—it’s only your imagination.",
        "author" => "Asif"
    ],
    [
        "quote" => "Push yourself, because no one else is going to do it for you.",
        "author" => "Varun"
    ],
    [
        "quote" => "Great things never come from comfort zones.",
        "author" => "Charan"
    ],
    [
        "quote" => "Dream it. Wish it. Do it.",
        "author" => "Balaji"
    ],
    [
        "quote" => "Believe in yourself and all that you are. Know that there is something inside you that is greater than any obstacle.",
        "author" => "Bhanu"
    ],
    [
        "quote" => "Don’t wait for opportunity. Create it.",
        "author" => "Hruthik"
    ]
];


return response()->json(['status'=>true,'data'=>$data]);
            
        }
      //end testing
      
      
      public function Otp(Request $request){
          
          $otp=7777;
          $templateid="677e16c1d6fc052d194f1a02";
          CustomHelper::sendOtp($request->phone,$otp,$templateid); 
          
      }
      
      
      public function verifyOtp(Request $request){
           CustomHelper::verifyOtp($request->phone,$request->otp); 
      }
      
      
      
       public function __construct(Msg91Service $msg91Service)
    {
        $this->msg91Service = $msg91Service;
    }

    public function sendSms(Request $request)
    {
        
         $rules = [
             'mobile_number' => 'required|numeric',
             'message' => 'required|string|max:160',
    
                ];

    // Step 2: Custom Error Messages (Optional)
    $messages = [
        'mobile_number.required' => 'The phone number is required.',
        'mobile_number.numeric' => 'The phone number must be numeric.',
        'message.required' => 'The Message required.',
        'message.string' => 'The Message allowed only string.',
        'message.max' => 'The Message allowed max 160 characters.',
      
    ];

    // Step 3: Validate Data
    $validator = Validator::make($request->all(), $rules, $messages);

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
       
        
     
        // Template ID from DLT
        $templateId = '1107173581904926603'; 

        // Variables for the template
        $variables = [
            'VAR1' => 'John',
            'VAR2' => '123456',
        ];

        $response = $this->msg91Service->sendSms($request->mobile_number, $templateId, $variables);
        
        dd($response);

        if (isset($response['error'])) {
            return response()->json(['error' => $response['error']], 500);
        }

        return response()->json(['success' => 'SMS sent successfully!']);
    }
       

       
       

      
    



    public function profile(){
        



        // return response()->json(auth()->user());
        
    }
    

    public function refresh(){
        return $this->resondedJwtToken(auth()->refresh());

    }
    
     public function verifyOtpReq(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'phone' => 'required|digits:10',
            'otp' => 'required|digits:4',
            
        ]);
        
      // dd($req->otp);
       // dd($req->phone);
    
        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json([
                 'status'=>false,
                 'message'=>$validator->errors()->first(),    
                ],
                
                200);
        }
    
     
          $phoneExists = User::where('phone', $req->phone)->exists();
           if (!$phoneExists) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Phone number does not exist.',
                ]);
            }
            
            $phone='+91'.$req->phone;
            
            
          
            
            //dd($phone);
         $status=CustomHelper::verifyOtp($phone,$req->otp); 
             //dd($status);
             
              if($status){
                  
                   $user = User::where('phone',$req->phone)->first();
            
            $token = auth()->login($user);

            // dd($ss);
             
      
        return $this->resondedJwtToken($token);
                  
              }else{
                  return response()->json([
                      'status'=>false,
                            
                      ]);
              }
              
              
              
      
       // return $this->resondedJwtToken($token);

        // Return the successful login response with the generated token
       

           }
    
      public function loginOtp(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'phone' => 'required|digits:10',
            
        ]);
        
       // dd($req->phone);
    
        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json([
                 'status'=>false,
                 'message'=>$validator->errors()->first(),    
                ],
                
                200);
        }
    
     
          $phoneExists = User::where('phone', $req->phone)->exists();
          
         // dd($phoneExists);
           if (!$phoneExists) {
    
                return response()->json([
                    'status' => false,
                    'message' => 'Phone number does not exist.',
                ]);
            }
            
            $phone='+91'.$req->phone;
            
            
            //dd($phone);
            $ss= CustomHelper::sendOtp($phone); 
            
           

        // Return the successful login response with the generated token
       

           }
    

    public function login(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'email' => 'required|email|string',
            'password' => 'required'
        ]);
    
        // If validation fails, return the errors
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        // Attempt to authenticate the user
        $credentials = $validator->validated(); // Extract validated credentials
        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'message' => 'Invalid Credentials',
                'status'=>false
            ], 422);  // You should return a 401 Unauthorized status
        }
      
        return $this->resondedJwtToken($token);

        // Return the successful login response with the generated token
       

           }

     protected function resondedJwtToken($token){
    
        $profile=auth()->user();
        $user = auth()->user()->user_type;
        $role="";
        if($user==0){
            $role="Admin";
        }elseif($user==1){
             $role="Customer";
        }elseif($user==2){
            $role="Doctor";
        }elseif($user==3){
            $role="Receptionist";
        }elseif($user==4){
            $role="Lab";
        }

            return response()->json([
            
            'access_token'=>$token,
            'status'=>true,
            'user'=>$role,
            'userDetails'=>$profile,
            'token_type'=>'bearer',
            'message'=>'Login Successfully Completed',
            'expires_in'=> auth()->factory()->getTTL()*120
            
            ]);
        
        }
        


    public function register(UserRequest $request)
    {
        // $validatedData = $request->validated();
      

      // dd($request->all());
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'username' => Str::slug($request->name) . rand(1, 500),
            'phone' => $request->phone,
            'user_type' => 1,
        ]);



           // $userId = 1; // Replace with the actual user ID
            $roleId = 2; // Replace with the actual role ID for "customer"

            DB::table('model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => 'App\Models\User', // Ensure this matches your user model's namespace
                'model_id' => $user->id,
            ]);



        
      
        if( $user){

          //  $token = $user->createToken('auth_token')->plainTextToken;
     // public static function create_user($phone,$name,$mail,$password)
               CustomHelper::create_user($request->phone,$request->name,$request->email,$request->password); 

          return response()->json([
            'message' => 'user registered successfully',
            'user'=>$user,
            'status'=>true
            
        ]);
    



        }else{

            return response()->json([
                'status'=>false,
                'message'=>'Registration Failes'
               
            ]);

        }
        
   
    }
    
    
    
       public function add_user_admin(Request $request)
    {
       
       //'app_type'=>$req->appointment_type,
      
        
    
     $rules = [
        'phone'      => 'required|numeric|unique:users,phone',
        'name'      => 'required',
        // 'email'=>'required'
       
       
    ];

    // Step 2: Custom Error Messages (Optional)
    $messages = [
        'phone.required' => 'The phone number is required.',
        'phone.numeric' => 'The phone number must be numeric.',
        'name.required' => 'The User name is required.',
      
    ];

    // Step 3: Validate Data
    $validator = Validator::make($request->all(), $rules, $messages);

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
       
       
       
       
       
     $email = Str::random(10).'@gmail.com';
    
      
       
       
       
       
       
        $user = User::create([
            'name' => $request->name,
            'email' => $email,
            'password' => Hash::make($request->phone),
            'username' => Str::slug($request->name) . rand(1, 500),
            'phone' => $request->phone,
          
            'user_type' => 1,
        ]);



           // $userId = 1; // Replace with the actual user ID
            $roleId = 2; // Replace with the actual role ID for "customer"

            DB::table('model_has_roles')->insert([
                'role_id' => $roleId,
                'model_type' => 'App\Models\User', // Ensure this matches your user model's namespace
                'model_id' => $user->id,
            ]);



        
      
        if( $user){

          //  $token = $user->createToken('auth_token')->plainTextToken;
    
        CustomHelper::create_user($request->phone,$request->name,$email,$request->phone); 

          return response()->json([
            'message' => 'user registered successfully',
            'user'=>$user,
            'status'=>true
            
        ]);
    



        }else{

            return response()->json([
                'status'=>false,
                'message'=>'Registration Failes'
               
            ]);

        }
        
   
    }

   
    public function sendResetLinkEmail(sendResetRequest $request)
    {
        // $request->validate(['email' => 'required|email']);

        
        $rand=rand(1111,9999);

        $user=User::where('email', $request->email)->get();

     
     
        $update = User::where('email', $request->email)
        ->update(['password_code' => $rand,'exp_time'=>now()->addMinutes(5)]);
      
        $data = ['Otp' => $rand, 'name' => $user[0]->name];
        
        
        Mail::to($request->email)->send(new SendEmailOtp($data));
        
        // Mail::to($request->email)->send(new SendPassword($data));
        
//         Mail::raw('This is a plain text email for testing purposes.', function ($message) {
//     $message->to('sk.asif0490@gmail.com')
//             ->subject('Test Plain Text Email');
// });
        
    
        //SendEmailJob::dispatch($data, $request->email);


       
       
        
      
        return response()->json([
            'status'=>true,
            'message'=>'verification Email sent to your registered email id'
        ]);
            

        
    }

  

    public function resetpassword(resetRequest $request){

      

        $get_data=User::where('password_code',$request->otp)->get();
        $expirationTime = Carbon::parse($get_data[0]->exp_time);
        $currentTime = Carbon::now();

        if ($currentTime->greaterThan($expirationTime)) {
            // The timestamp has expired

            return response()->json([
                'status'=>false,
                'message'=>'Time Expired',
            ]);
            //echo 'The timestamp has expired.';
        } else {
            // The timestamp has not expired
           

            $user = User::where('password_code',$request->otp)->update([
               
                'password' => Hash::make($request->password),
               
                'password_code'=>null,
                'exp_time'=>null
               
            ]);
        
            if($user){
                return response()->json([
                    'status' => true,
                    'message' => 'Password Successfully updated',
                ]);

            }else{
                return response()->json([
                    'status' => false,
                    'message' => 'Something went wrong please try again later',
                ]);

            }
           
           
            
           
        }


       

    }


    public function logout(Request $request)
{

    auth()->logout();


    return response()->json(['message'=>'User Successfully Loggout']);



}


}
