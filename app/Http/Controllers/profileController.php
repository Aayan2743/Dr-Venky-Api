<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Requests\updateProfileRequest;
use App\Http\Requests\updateProfilePictureRequest;
use Str;
use Hash;
use Illuminate\Support\Facades\Storage;

class profileController extends Controller
{
    //
    public function profileController(){
       
        $profile = auth()->user();

// Determine user type role
$user = $profile->user_type;
$role = "";
if ($user == 0) {
    $role = "Admin";
} elseif ($user == 1) {
    $role = "Customer";
} elseif ($user == 2) {
    $role = "Doctor";
} elseif ($user == 3) {
    $role = "Receptionist";
} elseif ($user == 4) {
    $role = "Lab";
}

// Add the role dynamically to the profile object
$profile->role = $role;

return response()->json([
    'status' => true,
    'user' => $role,
    'userDetails' => $profile,
]);



            // return response()->json(data: auth()->user());
      
    }

    // 'name'=>'Customer Name',
    // 'phone'=>'Customer phone',
    // 'email'=>'Customer Email',
    // 'state'=>'Customer State',
    // 'city'=>'Customer City',
    // 'password'=>'Customer Login Password',

    public function UpdateController(updateProfileRequest $req){


        // dd($req->password);

        if($req->password==null){
            $update_profile=user::where('id',auth()->user()->id)->update([
                'name'=>$req->name,
                'email'=>$req->email,
                'phone'=>$req->phone,
                'username' => Str::slug($req->name) . rand(1, 500),
            //    'password' => Hash::make($req->password),
                'state'=>$req->state,
                'city'=>$req->city,
            ]);
    
            if($update_profile==1){
                return response()->json([
                    'status'=>true,
                    'message'=>'User Details Updated Successfully....!'
                ]);
            }else{
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Nothing Updated....!'
                ]);
            }

        }else{

            $update_profile=user::where('id',auth()->user()->id)->update([
                'name'=>$req->name,
                'email'=>$req->email,
                'phone'=>$req->phone,
                'username' => Str::slug($req->name) . rand(1, 500),
               'password' => Hash::make($req->password),
                'state'=>$req->state,
                'city'=>$req->city,
            ]);
    
            if($update_profile==1){
                return response()->json([
                    'status'=>true,
                    'message'=>'User Details Updated Successfully....!'
                ]);
            }else{
    
                return response()->json([
                    'status'=>false,
                    'message'=>'Nothing Updated....!'
                ]);
            }

        }


       
        
    }


    public function UpdateProfilePictureController(updateProfilePictureRequest $request)
{
    // Validate the uploaded file
   
    // Get the authenticated user
    $user = Auth()->user();

    // Delete the old profile picture if it exists
    if ($user->profile_picture) {
        Storage::disk('public')->delete($user->profile_picture);
    }

    // Store the new profile picture
    $path = $request->file('profile_picture')->store('profile_pictures', 'public');

    // Update the user's profile picture path in the database
    $user->profile_picture = $path;
    $user->save();

    // Generate the full URL for the stored image
    $profilePictureUrl = Storage::url($path);

    // Respond with success and the new profile picture full URL
    return response()->json([
        'message' => 'Profile picture updated successfully.',
        'profile_picture_url' => url($profilePictureUrl), // Full URL with domain
    ], 200);



}

}
