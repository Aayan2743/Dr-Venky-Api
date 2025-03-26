<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\patientCategory;
use App\Models\mypet;
use App\Models\user;
use App\Models\pet;
use App\Http\Requests\petRequest;

class PatientCategoryController extends Controller
{
    //


    public function store_url(){
        return response()->json([
            'status'=>true,
            'url'=>env('STORE_URL')
        ]);
    }


    public function list_all_pet(){

        $pets=patientCategory::get();

        if(count($pets)>0){
            return response()->json([
                'status'=>true,
                'data'=>$pets
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'data'=>$pets
            ]);

        }

    }
    
    public function list_all_users_admin(){
        
        // 1 means only users
        $users=user::where('user_type',1)->get();
        
          if(count($users)>0){
            return response()->json([
                'status'=>true,
                'data'=>$users
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'data'=>$users
            ]);

        }
        
    }
    
    
    public function list_all_pet_admin($id){
        
          $pets=mypet::where('user_assigned',$id)->get();

        if(count($pets)>0){
            return response()->json([
                'status'=>true,
                'data'=>$pets
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'data'=>$pets
            ]);

        }
        
    }

    public function add_my_pet_details(petRequest $req){
        
        $add_pet=mypet::create([
            'petname'=>$req->petname,
            'petgender'=>$req->petgender,
            'petbread'=>$req->petbread,
            'category'=>$req->category,
            'petAge'=>$req->age,
            'petDob'=>$req->dateOfBirth,
            'petDobOptions'=>$req->petAgeOptions,
            'user_assigned'=>auth()->user()->id
        ]);

        if($add_pet){
            return response()->json([
                'status'=>true,
                'message'=>'Pet Created Successfully'
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Pet Not Created Successfully'
            ]);

        }
        
    }


}
