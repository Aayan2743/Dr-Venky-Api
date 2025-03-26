<?php
namespace App\Http\Controllers;
use Auth;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\appointment;
use App\Models\prescription;
use App\Models\medicanprecreprion;
use App\Models\mypet;
use App\Models\labreport;
use Carbon\Carbon;
class UserDashboard extends Controller
{
  
    
    
    
     public function UserHistory(Request $request,$id){


        $get_data=labreport::with('appiontment','user','pet','getAllSubservices')->where('uid',Auth()->user()->id)->get();
                
     





        


// new code



$query = appointment::with(['doctor_details', 'pet_details', 'prescriptions', 'labReports','prescriptions.medicines','labReports.getAllSubservices']) // Eager load related models
    ->where('user_id', Auth()->user()->id);  // Filter by authenticated user

// Optional: Add conditions based on query parameters like pet_id or appointment_date
$petId = $request->query('petid');
$dateofapointment = $request->query('appointmentdate');
$opNumber = $request->query('opnumber');
$prescriptionId = $id; // Search by prescription ID

if ($petId && $dateofapointment) {
    $query->where('pet_id', $petId)
        ->whereDate('dateofapp', $dateofapointment);
} elseif ($petId) {
    $query->where('pet_id', $petId);
} elseif ($dateofapointment) {
    $query->whereDate('dateofapp', $dateofapointment);
}

if ($opNumber) {
    $query->where('id', 'like', "%$opNumber%"); // Search for OP number (adjust if the column name differs)
}

// Add condition for searching prescriptions by ID
if ($prescriptionId) {
    $query->whereHas('prescriptions', function ($query) use ($prescriptionId) {
        $query->where('aaid', $prescriptionId); // Search prescriptions by their ID
    });
}

// Execute the query and fetch the results
$appointments = $query->orderBy('id', 'desc')->get();

//dd($appointments);

// Now, for each appointment, you will need to retrieve the subservices and fees for the prescriptions.
$appointments->map(function ($appointment) {
    // Assuming each appointment has a 'prescriptions' relationship (which is eager loaded)
    $appointment->prescriptions->map(function ($prescription) {
        // Call the getAllSubservicesWithFees method to retrieve subservices and fees
        $subservicesWithFees = $prescription->getAllSubservicesWithFees();
        
        // Add the subservices and fees to the prescription data
        $prescription->subservices_with_fees = $subservicesWithFees;
    });

    return $appointment;
});





// Return the data
return response()->json([
    'status' => true,
    'data' => $appointments,
   
]);



















       



    //     $petId = $request->query('petid');
    // $dateofapointment = $request->query('appointmentdate');
    
    // // Start building the query
    // $query = appointment::with(['doctor_details', 'pet_details', 'prescriptions'])
    //     ->where('user_id', Auth()->user()->id);  // Ensure you're only fetching for the logged-in user

    // // Apply filters conditionally based on provided query parameters
    // if ($petId) {
    //     $query->where('pet_id', $petId); // Filter by pet_id if provided
    // }

    // if ($dateofapointment) {
    //     $query->whereDate('dateofapp', $dateofapointment); // Filter by date if provided
    // }

    // // Fetch the filtered appointments, ordered by the date of appointment in descending order
    // $patient_details = $query->orderBy('dateofapp', 'desc')->get();

    // // Return the response as JSON
    // return response()->json([
    //     'status' => true,
    //     'data' => $patient_details
    // ]);













     
       
      
        
       
       
       
       
       
      
    }    
    
    
    
    public function history(Request $request){


        $get_data=labreport::with('appiontment','user','pet','getAllSubservices')->where('uid',Auth()->user()->id)->get();
                
     





        // Fetch appointments for the authenticated user with related models
// $query = appointment::with(['doctor_details', 'pet_details', 'prescriptions','labReports']) // Eager load related models
// ->where('user_id', Auth()->user()->id);  // Filter by authenticated user

// // Optional: Add conditions based on query parameters like pet_id or appointment_date
// $petId = $request->query('petid');
// $dateofapointment = $request->query('appointmentdate');
// $opNumber = $request->query('opnumber');

// if ($petId && $dateofapointment) {
// $query->where('pet_id', $petId)
//       ->whereDate('dateofapp', $dateofapointment);
// } elseif ($petId) {
// $query->where('pet_id', $petId);
// } elseif ($dateofapointment) {
// $query->whereDate('dateofapp', $dateofapointment);
// }

// if ($opNumber) {
//     $query->where('id', 'like', "%$opNumber%"); // Search for OP number (adjust if the column name differs)
// }


// // Execute the query and fetch the results
// $appointments = $query->orderBy('id', 'desc')->get();

// // Now, for each appointment, you will need to retrieve the subservices and fees for the prescriptions.
// $appointments->map(function ($appointment) {
// // Assuming each appointment has a 'prescriptions' relationship (which is eager loaded)
// $appointment->prescriptions->map(function ($prescription) {
//     // Call the getAllSubservicesWithFees method to retrieve subservices and fees
//     $subservicesWithFees = $prescription->getAllSubservicesWithFees();
    
//     // Add the subservices and fees to the prescription data
//     $prescription->subservices_with_fees = $subservicesWithFees;
// });

// return $appointment;
// });

// // Return the data
// return response()->json([
// 'status' => true,
// 'data' => $appointments,

// ]);


// new code



$query = appointment::with(['doctor_details', 'pet_details', 'prescriptions', 'labReports','prescriptions.medicines','labReports.getAllSubservices']) // Eager load related models
    ->where('user_id', Auth()->user()->id);  // Filter by authenticated user

// Optional: Add conditions based on query parameters like pet_id or appointment_date
$petId = $request->query('petid');
$dateofapointment = $request->query('appointmentdate');
$opNumber = $request->query('opnumber');
$prescriptionId = $request->query('prescriptionid'); // Search by prescription ID

if ($petId && $dateofapointment) {
    $query->where('pet_id', $petId)
        ->whereDate('dateofapp', $dateofapointment);
} elseif ($petId) {
    $query->where('pet_id', $petId);
} elseif ($dateofapointment) {
    $query->whereDate('dateofapp', $dateofapointment);
}

if ($opNumber) {
    $query->where('id', 'like', "%$opNumber%"); // Search for OP number (adjust if the column name differs)
}

// Add condition for searching prescriptions by ID
if ($prescriptionId) {
    $query->whereHas('prescriptions', function ($query) use ($prescriptionId) {
        $query->where('aaid', $prescriptionId); // Search prescriptions by their ID
    });
}

// Execute the query and fetch the results
$appointments = $query->orderBy('id', 'desc')->get();

// Now, for each appointment, you will need to retrieve the subservices and fees for the prescriptions.
$appointments->map(function ($appointment) {
    // Assuming each appointment has a 'prescriptions' relationship (which is eager loaded)
    $appointment->prescriptions->map(function ($prescription) {
        // Call the getAllSubservicesWithFees method to retrieve subservices and fees
        $subservicesWithFees = $prescription->getAllSubservicesWithFees();
        
        // Add the subservices and fees to the prescription data
        $prescription->subservices_with_fees = $subservicesWithFees;
    });

   
    return $appointment;
});


$hasPrescription = false;

foreach ($appointments as $app) {
    if ($app->prescription_id == null) {
        $hasPrescription = true;
        break; // Exit loop early if we find at least one valid appointment
    }
}

 $medicains=medicanprecreprion::where('pid',96)->get();
if ($hasPrescription) {
    return response()->json([
        'status' => true,
        'data' => $appointments,
        
        
    ]);
} else {
    return response()->json([
        'status' => false,
        'data' => $appointments,
    ]);
}





// dd($appointments);

// $appointments = $appointments->filter(function ($appointment) {
//     return !is_null($appointment->dr_id); // Keep only appointments where dr_id is NOT null
// })->map(function ($appointment) {
//     $appointment->prescriptions->map(function ($prescription) {
//         $prescription->subservices_with_fees = $prescription->getAllSubservicesWithFees();
//     });

//     return $appointment;
// });


// $medicains=medicanprecreprion::where('pid',96)->get();

// Return the data
// return response()->json([
//     'status' => true,
//     'data' => $appointments,
//     //  'medicains' => $medicains,
// ]);



















       



    //     $petId = $request->query('petid');
    // $dateofapointment = $request->query('appointmentdate');
    
    // // Start building the query
    // $query = appointment::with(['doctor_details', 'pet_details', 'prescriptions'])
    //     ->where('user_id', Auth()->user()->id);  // Ensure you're only fetching for the logged-in user

    // // Apply filters conditionally based on provided query parameters
    // if ($petId) {
    //     $query->where('pet_id', $petId); // Filter by pet_id if provided
    // }

    // if ($dateofapointment) {
    //     $query->whereDate('dateofapp', $dateofapointment); // Filter by date if provided
    // }

    // // Fetch the filtered appointments, ordered by the date of appointment in descending order
    // $patient_details = $query->orderBy('dateofapp', 'desc')->get();

    // // Return the response as JSON
    // return response()->json([
    //     'status' => true,
    //     'data' => $patient_details
    // ]);













     
       
      
        
       
       
       
       
       
      
    }
    public function index(Request $request){

        // dd("fdjghdfkjghdfg");
    //     $today=Carbon::today();
    //   $get_total_appointment=appointment::where('user_id',Auth()->user()->id)->count();
    //   $get_todays_appointment=appointment::whereDate('dateofapp',$today)->where('user_id',Auth()->user()->id)->count();
    //   $get_pet_details=mypet::where('user_assigned',Auth()->user()->id)->count();
    //   $get_my_pet_details=mypet::where('user_assigned',Auth()->user()->id)->get();


       
    //   $petId = $request->query('petid');
    //   $dateofapointment = $request->query('appointmentdate');

    //   $query = appointment::with('doctor_details', 'pet_details')
    //   ->where('user_id', Auth()->user()->id);

    //   if ($petId && $dateofapointment) {
    //     $query->where('pet_id', $petId)
    //           ->whereDate('dateofapp', $dateofapointment);
    // } elseif ($petId) { 
    //     // Check for pet only
    //     $query->where('pet_id', $petId);
    // } elseif ($dateofapointment) {
    //     // Check for date only
    //     $query->whereDate('dateofapp', $dateofapointment);
    // }


    // $patient_details = $query->orderBy('dateofapp', 'desc')->get();
    // return response()->json([


    //     'status'=>true,
    //      'get_total_appointment'=>$get_total_appointment,
    //     'get_todays_appointment'=>$get_todays_appointment,
    //     'get_pet_details'=>$get_pet_details,    
    //     'get_recent_appointment'=>$patient_details,
        
    //             'get_my_pet_details'=>$get_my_pet_details,   
        
        
       
        
    // ]);


$today = Carbon::today();
$get_total_appointment = appointment::where('user_id', Auth()->user()->id)->count();
$get_todays_appointment = appointment::whereDate('dateofapp', $today)
    ->where('user_id', Auth()->user()->id)
    ->count();
$get_pet_details = mypet::where('user_assigned', Auth()->user()->id)->count();
$get_my_pet_details = mypet::where('user_assigned', Auth()->user()->id)->get();

$petId = $request->query('petid');
$dateofapointment = $request->query('appointmentdate');
$opNumber = $request->query('opnumber');

$query = appointment::with('doctor_details', 'pet_details')
    ->where('user_id', Auth()->user()->id);

if ($petId && $dateofapointment) {
    $query->where('pet_id', $petId)
          ->whereDate('dateofapp', $dateofapointment);
} elseif ($petId) {
    $query->where('pet_id', $petId);
} elseif ($dateofapointment) {
    $query->whereDate('dateofapp', $dateofapointment);
}

if ($opNumber) {
    $query->where('id', 'like', "%$opNumber%"); // Search for OP number (adjust if the column name differs)
}


// Add ordering by `op_number` in ascending order
$patient_details = $query
    ->orderBy('id', 'desc') // Order by op_number in ascending order
    ->orderBy('dateofapp', 'asc') // Optionally, secondary ordering by dateofapp in descending order
    ->get();

return response()->json([
    'status' => true,
    'get_total_appointment' => $get_total_appointment,
    'get_todays_appointment' => $get_todays_appointment,
    'get_pet_details' => $get_pet_details,
    'get_recent_appointment' => $patient_details,
    'get_my_pet_details' => $get_my_pet_details,
]);




    //    if ($petId) {
    //     $get_recent_appointment = appointment::with(relations: 'pet_details')
    //     ->where('user_id', Auth()->user()->id)
    //     ->where('pet_id',$petId)
    //     ->orderBy('dateofapp', 'desc')
    //     ->take(4)
    //     ->get();
    //     return response()->json([
    //         'status'=>true,
    //         'get_total_appointment'=>$get_total_appointment,
    //         'get_todays_appointment'=>$get_todays_appointment,
    //         'get_pet_details'=>$get_pet_details,
    //         'get_recent_appointment'=>$get_recent_appointment,
    //         'get_my_pet_details'=>$get_my_pet_details
    //        ]);
    //     // Replace with your logic
    // }
    // if ($dateofapointment) {
    //     $get_recent_appointment = appointment::with(relations: 'pet_details')
    //     ->where('user_id', Auth()->user()->id)
    //     ->whereDate('dateofapp',$dateofapointment)
    //     ->take(4)
    //     ->get();
    //     return response()->json([
    //         'status'=>true,
    //         'get_recent_appointment_by_date'=>$get_recent_appointment,
    //         'get_total_appointment'=>$get_total_appointment,
    //         'get_todays_appointment'=>$get_todays_appointment,
    //         'get_pet_details'=>$get_pet_details,
    //         // 'get_recent_appointment'=>$get_recent_appointment,
    //         'get_my_pet_details'=>$get_my_pet_details
    //        ]);
    //     // Replace with your logic
    // }




    //    $get_recent_appointment = appointment::with(relations: 'pet_details')
    //         ->where('user_id', Auth()->user()->id)
    //         ->orderBy('dateofapp', 'desc')
    //         ->take(4)
    //         ->get();
    // //    $get_recent_appointment=appointment::where('user_id',Auth()->user()->id)->get();
    //    return response()->json([
    //     'status'=>true,
    //     'get_total_appointment'=>$get_total_appointment,
    //     'get_todays_appointment'=>$get_todays_appointment,
    //     'get_pet_details'=>$get_pet_details,
    //     'get_recent_appointment'=>$get_recent_appointment,
    //     'get_my_pet_details'=>$get_my_pet_details
    //    ]);
    }
    public function pet_details(Request $request){
        $petId = $request->query('petid');
        $get_recent_appointment = appointment::with(relations: 'pet_details')
        ->where('user_id', Auth()->user()->id)
        ->where('pet_id',$petId)
        ->orderBy('dateofapp', 'desc')
        ->take(4)
        ->get();
        return response()->json([
            'status'=>true,
            'get_recent_appointment'=>$get_recent_appointment,
           ]);
    }
}
