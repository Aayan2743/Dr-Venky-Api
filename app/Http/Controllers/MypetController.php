<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\mypet;
use App\Http\Requests\myPetAddDetails;
use Illuminate\Support\Arr;

class MypetController extends Controller
{
    //
    public function get_my_pet_details(){

        //$my_pets=mypet::where('user_assigned',Auth()->user()->id)->get();
        
        $my_pets = mypet::where('user_assigned', Auth()->user()->id)
    ->orderBy('id', 'desc') // Sort by id in descending order
    ->get();

        return response()->json([
            'status'=>true,
            'data'=>$my_pets
        ]);
    }


    public function add_pet_details(myPetAddDetails $req){


        $create_pet=mypet::create([
            'petname'=>$req->petname,
            'petgender'=>$req->petgender,
            'petbread'=>$req->petbread,
            'category'=>$req->category ,
            'user_assigned'=>auth()->user()->id,
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


    }
    
    
    public function get_my_pets($id){
        
        $pet_details=mypet::find($id);
        
        return response()->json([
            'status'=>true,
            'data'=>$pet_details
            ]);
        
      //  dd($pet_details);
        
    }
    
    
    public function update(Request $req,$id){
        
    $rules = [
         'petname' => 'required|String',
         'petgender' => 'required|in:F,M',
         'petbread' => 'required|String',
         'category' => 'required|String',
         'petAge' => 'required|integer',
         'dateOfBirth' => 'required|date|before_or_equal:today',
            'petDobOptions' => 'required|in:Days,Months,Years',
         
       
           
            
       
        
        
    ];

    // Custom attribute names
    $attributes = [
         'petname' => 'Pet Name',
        'petgender' => 'Pet Geneder',
        'petbread' => 'Pet Bread',
        'category' => 'Pet Category',
        'petAge' => 'Pet Age',
        'dateOfBirth' => 'Pet Date Of Birth',
        'petDobOptions' => 'Pet Age Options',
       
    ];

    // Custom error messages
    $messages = [
       
        'petname.required' => 'Pet Name Required',
        'petgender.required' => 'Pet Gender Required',
        'petgender.in' => 'Pet Gender Allowed any one of following F or M',
        'petbread.required' => 'Pet Bread Required',
        'category.required' => 'Pet Category Required',
        'petAge.required' => 'Pet Age Required',
        
    ];
        
         try{
            
             $validatedData = $req->validate($rules, $messages, $attributes);
             
             $check_data=mypet::where('id',$id)->count();
             
             if($check_data>0){
                 
                 $update_pet=mypet::where('id',$id)->update([
                     'petname'=>$req->petname,
                     'petgender'=>$req->petgender,
                     'petbread'=>$req->petbread,
                     'category'=>$req->category,
                     'petAge'=>$req->petAge,
                
                    'petDob'=>$req->dateOfBirth,
                    'petDobOptions'=>$req->petDobOptions,
                    'user_assigned'=>auth()->user()->id
                     
                     
                     ]);
                     
                if($update_pet==1){
                    return response()->json([
                               'status'=>true,
                               'message'=>'Pet Details updated successfully....!',
                          ]);
                    
                }else{
                    
                    return response()->json([
                               'status'=>false,
                               'message'=>'Nothing updated....!',
                          ]);
                    
                }     
                 
             }else{
                 return response()->json([
                               'status'=>false,
                               'message'=>'Invalid Pet ID',
                          ]);    
                 
             }
             
               
        }
        
        catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => false,
                    'message' => Arr::first(Arr::flatten($e->errors())),
                ], 422);  
            }
        
        
        // catch(\Illuminate\Validation\ValidationException $e){
        //   return response()->json([
        //         'status' => false,
        //         'message' => $e->errors(),
        //     ], 422);  
        // }
        
        
    }
    
      public function updateAdmin(Request $req,$id){
        
    $rules = [
         'petname' => 'required|String',
         'petgender' => 'required|in:F,M',
         'petbread' => 'required|String',
         'category' => 'required|String',
         'petAge' => 'required|integer',
         'dateOfBirth' => 'required|date|before_or_equal:today',
            'petDobOptions' => 'required|in:Days,Months,Years',
         
       
           
            
       
        
        
    ];

    // Custom attribute names
    $attributes = [
         'petname' => 'Pet Name',
        'petgender' => 'Pet Geneder',
        'petbread' => 'Pet Bread',
        'category' => 'Pet Category',
        'petAge' => 'Pet Age',
        'dateOfBirth' => 'Pet Date Of Birth',
        'petDobOptions' => 'Pet Age Options',
       
    ];

    // Custom error messages
    $messages = [
       
        'petname.required' => 'Pet Name Required',
        'petgender.required' => 'Pet Gender Required',
        'petgender.in' => 'Pet Gender Allowed any one of following F or M',
        'petbread.required' => 'Pet Bread Required',
        'category.required' => 'Pet Category Required',
        'petAge.required' => 'Pet Age Required',
        
    ];
        
         try{
            
             $validatedData = $req->validate($rules, $messages, $attributes);
             
             $check_data=mypet::where('id',$id)->count();
             
             if($check_data>0){
                 
                 $update_pet=mypet::where('id',$id)->update([
                     'petname'=>$req->petname,
                     'petgender'=>$req->petgender,
                     'petbread'=>$req->petbread,
                     'category'=>$req->category,
                     'petAge'=>$req->petAge,
                
                    'petDob'=>$req->dateOfBirth,
                    'petDobOptions'=>$req->petDobOptions,
                    // 'user_assigned'=>auth()->user()->id
                     
                     
                     ]);
                     
                if($update_pet==1){
                    return response()->json([
                               'status'=>true,
                               'message'=>'Pet Details updated successfully....!',
                          ]);
                    
                }else{
                    
                    return response()->json([
                               'status'=>false,
                               'message'=>'Nothing updated....!',
                          ]);
                    
                }     
                 
             }else{
                 return response()->json([
                               'status'=>false,
                               'message'=>'Invalid Pet ID',
                          ]);    
                 
             }
             
               
        }
        
        catch (\Illuminate\Validation\ValidationException $e) {
                return response()->json([
                    'status' => false,
                    'message' => Arr::first(Arr::flatten($e->errors())),
                ], 422);  
            }
        
        
        // catch(\Illuminate\Validation\ValidationException $e){
        //   return response()->json([
        //         'status' => false,
        //         'message' => $e->errors(),
        //     ], 422);  
        // }
        
        
    }
    
    
}
