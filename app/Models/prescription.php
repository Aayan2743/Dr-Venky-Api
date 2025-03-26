<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class prescription extends Model
{
    use HasFactory;

    protected $guarded=[];

    protected $table = 'prescriptions';

    // protected $fillable = ['aaid', 'inhouse', 'other_columns']; 


    // public function petDetails(){
    //     $this->hasOne(mypet::class,foreignKey: 'pid','id');
    // }
    
    public function medicines()
    {
        return $this->hasMany(medicanprecreprion::class, 'pid', 'id');
    }


    public function pet()
    {
        return $this->belongsTo(mypet::class, 'pid', 'id'); // Adjust 'pid' and 'id' as necessary
    }


    public function getAllSubservicesWithFees1()
{
    $inhouse = $this->relatedInhouseSubservices();  // Get inhouse subservices
    $grooming = $this->relatedgroomingSubservices();  // Get grooming subservices
    $general_services = $this->relatedservicesSubservices();  // Get general services subservices
    $lab = $this->relatedlabSubservices();  // Get lab subservices

    // Merge all subservices into one collection
    $allSubservices = $inhouse->merge($grooming)
                              ->merge($general_services)
                              ->merge($lab);

    // Calculate the total amount of all subservices
    $totalAmount = $allSubservices->sum('fee');

    // Flatten the collection to match your required structure, including service name
    $subserviceData = $allSubservices->map(function ($subservice) {
        // Fetch the service name based on service_id
        $serviceName = $subservice->service ? $subservice->service->name : 'Unknown Service';

        return [
            'id' => $subservice->id,
            'subservicename' => $subservice->subservicename,
            'service_id' => $subservice->service_id,  // Assuming service_id is available
            'service_name' => $serviceName,  // Add the service name here
            'fee' => $subservice->fee,
        ];
    });

    // Return the total amount and subservice data
    return [
        'totalAmount' => $totalAmount,
        'subserviceData' => $subserviceData
    ];
}



    public function getAllSubservicesWithFees()
    {
        $inhouse = $this->relatedInhouseSubservices();  // Get inhouse subservices
        $grooming = $this->relatedgroomingSubservices();  // Get grooming subservices
        $general_services = $this->relatedservicesSubservices();  // Get general services subservices
        $lab = $this->relatedlabSubservices();  // Get lab subservices

        // Merge all subservices into one collection
        $allSubservices = $inhouse->merge($grooming)
                                  ->merge($general_services)
                                  ->merge($lab);



                                  $totalAmount = $allSubservices->sum('fee');

                                  // Flatten the collection to match your required structure
                                  $subserviceData = $allSubservices->map(function ($subservice) {
                                      return [
                                          'id' => $subservice->id,
                                          'subservicename' => $subservice->subservicename,
                                          'service_id' => $subservice->service_id,  // Assuming service_id is available
                                          'fee' => $subservice->fee,
                                      ];
                                  });
                          
                                  // Add the total amount at the end of the collection
                                //   $subserviceData->push(['totalAmount' => $totalAmount]);
                          


                                //   return $subserviceData;        
                                   return [
                                                'totalAmount'=>$totalAmount,
                                                 'subserviceData'=>$subserviceData   

                                   ];        
                       


    }


   

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id'); // 'uid' is the foreign key in prescriptions, 'id' is the primary key in users
    }

    public function dr_user()
    {
        return $this->belongsTo(User::class, 'dr_id', 'id'); // 'uid' is the foreign key in prescriptions, 'id' is the primary key in users
    }

    public function appointment_details()
    {
        return $this->belongsTo(appointment::class, 'aaid', 'id'); // 'uid' is the foreign key in prescriptions, 'id' is the primary key in users
    }

    public function inhouse()
    {
        return $this->belongsTo(appointment::class, 'aaid', 'id'); // 'uid' is the foreign key in prescriptions, 'id' is the primary key in users
    }

   


    public function relatedInhouseSubservices()
    {
        // Decode the 'inhouse' column (which stores the subservice IDs as a JSON string)
        $subserviceIds = json_decode($this->inhouse, true);  // Decode as an array

        // Check if the decoded result is an array (valid JSON)
        if (!is_array($subserviceIds)) {
            return collect();  // Return an empty collection if not a valid array
        }

        // Query subservices where the id is in the decoded array
        return subservice::whereIn('id', $subserviceIds)->get();  // Retrieve the subservices by ID
    }


    public function relatedgroomingSubservices()
    {
        // Decode the 'inhouse' column (which stores the subservice IDs as a JSON string)
        $subserviceIds = json_decode($this->grooming, true);  // Decode as an array

        // Check if the decoded result is an array (valid JSON)
        if (!is_array($subserviceIds)) {
            return collect();  // Return an empty collection if not a valid array
        }

        // Query subservices where the id is in the decoded array
        return subservice::whereIn('id', $subserviceIds)->get();  // Retrieve the subservices by ID
    }


    public function relatedlabSubservices()
    {
        // Decode the 'inhouse' column (which stores the subservice IDs as a JSON string)
        $subserviceIds = json_decode($this->lab, true);  // Decode as an array

        // Check if the decoded result is an array (valid JSON)
        if (!is_array($subserviceIds)) {
            return collect();  // Return an empty collection if not a valid array
        }

        // Query subservices where the id is in the decoded array
        return subservice::whereIn('id', $subserviceIds)->get();  // Retrieve the subservices by ID
    }

    public function relatedservicesSubservices()
    {
        // Decode the 'inhouse' column (which stores the subservice IDs as a JSON string)
        $subserviceIds = json_decode($this->services, true);  // Decode as an array

        // Check if the decoded result is an array (valid JSON)
        if (!is_array($subserviceIds)) {
            return collect();  // Return an empty collection if not a valid array
        }

        // Query subservices where the id is in the decoded array
        return subservice::whereIn('id', $subserviceIds)->get();  // Retrieve the subservices by ID
    }


    public function appointment()
    {
        return $this->belongsTo(appointment::class, 'aaid', 'id'); // 'appointment_id' is the foreign key in the prescriptions table
    }


}
