<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use Illuminate\Http\StoreserviceRequest;
use App\Http\Requests\StoreserviceRequest;
use App\Http\Requests\StoresubserviceRequest;
use App\Models\service;
use App\Models\subservice;


class servicerelatedcontroller extends Controller
{
    //


    public function list_sub_service($id){

        $list_sub_services=subservice::where('service_id',$id)->get();

        


        if(count($list_sub_services)>0){
            return response()->json([
                'status'=>true,
                'data'=>$list_sub_services
            ]);
        }else{

            return response()->json([
                'status'=>false,
                'data'=>$list_sub_services
            ]);

        }


    }


    public function list_service(){

        $list_services=service::get();

        if(count($list_services)>0){
            return response()->json([
                'status'=>true,
                'data'=>$list_services
            ]);
        }else{

            return response()->json([
                'status'=>false,
                'data'=>$list_services
            ]);

        }
        
        
    }


    public function add_service(StoreserviceRequest $req){

        $createService=service::create([
            'service_name'=>$req->service_name,
        ]);

        if($createService){
            return response()->json([
                'status'=>true,
                'message'=>'service created',
            ]);
        }else{
            return response()->json([
                'status'=>false,
                'message'=>'service Not created',
            ]);
        }
           
    }

    public function add_sub_service(StoresubserviceRequest $req){

        $create_sub_service=subservice::create([
            'subservicename'=>$req->subservice_name,
            'service_id'=>$req->service_id,
        ]);

        if($create_sub_service){
            return response()->json([
                'status'=>true,
                'message'=>'Sub Service created',
            ]);

        }else{
            return response()->json([
                'status'=>false,
                'message'=>'Sub Service Not created',
            ]);


        }


    }

}
