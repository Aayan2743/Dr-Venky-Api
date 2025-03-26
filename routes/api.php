<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\CustomHelper;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
// Route::middleware(['auth:sanctum','auth:sanctum'])->get('/user', function (Request $request) {
//     return $request->user();
// });
use App\Http\Controllers\AuthController;
use App\Http\Controllers\servicerelatedcontroller;
use App\Http\Controllers\PatientCategoryController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\profileController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\MypetController;
use App\Http\Controllers\UserDashboard;
use App\Http\Controllers\ReceiptionistDashboard;
use App\Http\Controllers\transactiondetails;
use App\Http\Controllers\RazorpayController;
use Illuminate\Support\Facades\Mail;


use Illuminate\Support\Facades\Log;



Route::get('/send-test-mail', function () {
    $details = [
        'name' => 'John Doe',
        'message' => 'This is a test email from Laravel without using a Mailable class.'
    ];

    try {
        Mail::raw($details['message'], function ($message) {
            $message->to('sk.asif0490@gmail.com')
                    ->subject('Test Mail from Laravel');
        });

        return response()->json(['message' => 'Test mail sent successfully!']);
    } catch (\Exception $e) {
        Log::error('Email send failed: ' . $e->getMessage());
        return response()->json(['message' => 'Failed to send test email. Please check the logs for details.'], 500);
    }
});


Route::get('/test-helper/{phone}/{message}', function ($ph,$msg) {

  //  test1($ph,$msg);
    

   return CustomHelper::test1($ph,$msg);
});

// Route::get('/generate-pdf/{id}', [AppointmentController::class, 'generateA4Pdf']);

Route::get('/Quatation',[AuthController::class, 'Quatation']);

Route::post('/send-sms', [AuthController::class, 'sendSms']);

  Route::post('/OtpSend',[AuthController::class,'Otp']);


//   For Bharath Sir
  Route::get('/products',[AppointmentController::class,'products']);
  Route::get('/products1',[AppointmentController::class,'products1']);
  Route::get('/Orders',[AppointmentController::class,'Orders']);
  Route::get('/Orders1',[AppointmentController::class,'Orders_print']);
  Route::get('/OrdersHsnCode',[AppointmentController::class,'OrdersHsnCode']);
  Route::get('/OrderHsnCodeFiler',[AppointmentController::class,'OrderHsnCodeFiler']);  // working as per client
  Route::get('/ProductWiseReport',[AppointmentController::class,'ProductWiseReport']);  // working as per client
  Route::get('/StateWiseReports',[AppointmentController::class,'StateWiseReports']);  // working as per client
  Route::get('/orderDetails',[AppointmentController::class,'orderDetails']);  // Order Details as per client
  Route::get('/marginReports',[AppointmentController::class,'orderDetailsWithMargin']);  // margin product Details as per client
  Route::get('/marginReportsCategory',[AppointmentController::class,'marginReportsCategory']);  // margin product Details as per client
  Route::get('/pull-order/{oid}',[AppointmentController::class,'pullOrder']);
  Route::get('/all_orders',[AppointmentController::class,'allOrder']);
  
  Route::get('/export-order-products', [AppointmentController::class, 'exportOrderProducts']);  // 
//   For Bharath Sir  
  
  
  Route::get('/get_time_slots',[AppointmentController::class,'get_time_slots']);
  
    Route::get('/get_fee_details/{id}',[AppointmentController::class,'get_fee_details']);


Route::post('/login', [AuthController::class, 'login']);
Route::post('/loginOtp', [AuthController::class, 'loginOtp']);
  Route::post('/verifyOtp',[AuthController::class,'verifyOtpReq']);
// Route::post('/verifyOtp', [AuthController::class, 'verifyOtp']);
Route::post('/register', [AuthController::class, 'register']);
 Route::post('/password-reset', [AuthController::class, 'sendResetLinkEmail']);
 Route::post('/password-update', [AuthController::class, 'resetpassword']);
 
//  Route::post('/create-order', [RazorpayController::class, 'createOrder']);
//  Route::post('/verify-payment', [RazorpayController::class, 'verifyPayment']);

// Route::post('/callback', [RazorpayController::class, 'callback']);




 Route::group(['middleware'=>'jwt.verify'],function($routes){
     
      Route::post('/create-order', [RazorpayController::class, 'createOrder']);
 Route::post('/verify-payment', [RazorpayController::class, 'verifyPayment']);

Route::post('/callback', [RazorpayController::class, 'callback']);
     
     
     
 Route::get('/logout', [AuthController::class, 'logout']);
 Route::get('/refresh',[AuthController::class,'refresh']);
//  Route::get('/profile',[AuthController::class,'profile']);
 
 Route::post('/add_service',[servicerelatedcontroller::class,'add_service']);
 Route::post('/add_sub_service',[servicerelatedcontroller::class,'add_sub_service']);
 Route::get('/list_service',[servicerelatedcontroller::class,'list_service']);
 Route::get('/list_sub_service/{id}',[servicerelatedcontroller::class,'list_sub_service']);

 Route::get('/list_pet',[PatientCategoryController::class,'list_pet']);

//  workging 19-11-2024 New Version
 Route::post('/add_appointment',[AppointmentController::class,'add_appointment']);  // CHANGE
 Route::post('/check_user_admin',[AppointmentController::class,'add_appointment_admin']);  
 Route::post('/add_pet_admin',[AppointmentController::class,'add_pet_admin']);  
 Route::post('/cancel_appointment_admin/{id}',[AppointmentController::class,'cancel_appointment']); 
 Route::post('/add_user_admin', [AuthController::class, 'add_user_admin']);
 Route::post('/add_appointment_admins', [AppointmentController::class, 'book_appointment']);
 
  Route::get('/get_e_consultancy', [AppointmentController::class, 'get_e_consultancy']);
 
 Route::get('/receipt/{id}', [AppointmentController::class, 'generateReceipt']);
 Route::get('/receipt_all/{id}', [AppointmentController::class, 'generateReceipt_all']);
 Route::get('/receipt_all_print/{id}', [AppointmentController::class, 'generateReceipt_all_print']);
 
 
 Route::get('/get_user_pets',[AppointmentController::class,'user_pets']);
//  if needed use below
 Route::get('/list_user_category/{id}',[AppointmentController::class,'list_user_category']);


 Route::post('/add_my_pets',[PatientCategoryController::class,'add_my_pet_details']);
 Route::get('/list_all_pet',[PatientCategoryController::class,'list_all_pet']);
 Route::get('/list_all_pet_admin/{id}',[PatientCategoryController::class,'list_all_pet_admin']);
 Route::get('/list_all_users_admin',[PatientCategoryController::class,'list_all_users_admin']);
 Route::get('/store_url',[PatientCategoryController::class,'store_url']);



// add services
 Route::post('/add_services',[ReceiptionistDashboard::class,'add_services']);
 Route::get('/list_services',[ReceiptionistDashboard::class,'list_services']);
 Route::put('/get_services/{id}',[ReceiptionistDashboard::class,'get_services']);
 Route::post('/update_services/{id}',[ReceiptionistDashboard::class,'update_services']);

// add subservice
 Route::post('/add_sub_services',[ReceiptionistDashboard::class,'add_sub_services']);
Route::get('/generate-precreption-for-user/{id}', [ReceiptionistDashboard::class, 'generateA4Pdf']);

/* Doctor Dashboard */

Route::get('/doctors_dashboard',[DoctorController::class,'index']);





/*
  Dashboard For User
  with Query parament date and petname
*/
Route::get('/user_dashboard',[UserDashboard::class,'index']);
Route::get('/user_appointment_history',[UserDashboard::class,'history']);
Route::get('/user_appointment_history_details/{id}',[UserDashboard::class,'UserHistory']);

/*
  Dashboard For Receiptionist
  with Query parament date and petname
*/
Route::get('/receiptionist_dashboard',[ReceiptionistDashboard::class,'index']);
Route::get('/receiptionist_dashboard_all',[ReceiptionistDashboard::class,'all']);
Route::get('/doctor_dashboard_all',[ReceiptionistDashboard::class,'doctor_all']);
Route::get('/assign_to_doctor',[ReceiptionistDashboard::class,'assign_to_doctor']);
Route::post('/appointment_confirmed',[ReceiptionistDashboard::class,'appointment_confirmed']);
Route::post('/appointment_confirmed_for_econsutancy',[ReceiptionistDashboard::class,'appointment_confirmed_econsulancy']);
Route::get('/appointment_by_id/{id}',[ReceiptionistDashboard::class,'appointment_by_id']);
Route::get('/appointment_confirmed_patients',[ReceiptionistDashboard::class,'appointment_confirmed_patients']);
Route::get('/write_prescription/{id}',[ReceiptionistDashboard::class,'add_prescription']);
Route::post('/write_prescription',[ReceiptionistDashboard::class,'create_prescription']);

//reminder

Route::post('/create_reminders',[AppointmentController::class,'create']);
Route::get('/get_reminders/{id}',[AppointmentController::class,'edit']);
Route::get('/get_all_reminders',[AppointmentController::class,'allreminders']);
Route::post('/update_reminder/{id}/update',[AppointmentController::class,'update']);
Route::delete('/delete_reminder/{id}/delete',[AppointmentController::class,'delete']);

Route::get('/show_prescription/{id}',[ReceiptionistDashboard::class,'show_prescription_bk12']);

// Route::get('/show_prescription1/{id}',[ReceiptionistDashboard::class,'show_prescription']);
Route::get('/confirmation_prescription/{id}',[ReceiptionistDashboard::class,'confirmation_prescription']); // not using
Route::get('/lab_reports',[ReceiptionistDashboard::class,'lab_reports']);
Route::post('/upload_reports',[ReceiptionistDashboard::class,'upload_reports']);
Route::get('/download_reports/{id}',[ReceiptionistDashboard::class,'download_reports']);


Route::post('/add_staff',[DoctorController::class,'add_staff']);
Route::get('/get_staff',[DoctorController::class,'getStaff']);

Route::get('/get_staff_by_id/{id}',[DoctorController::class,'get_staff_by_id']);
Route::post('/update_staff_by_id/{id}',[DoctorController::class,'storeOrUpdateStaff']);
Route::post('/inactive_staff/{id}',[DoctorController::class,'delete_staff']);

Route::get('/get_all_transaction',[transactiondetails::class,'get_all_transaction']);
Route::get('/get_all_transaction_user',[transactiondetails::class,'get_all_transaction_user1']);
Route::get('/get_all_transaction_user_grouping',[transactiondetails::class,'get_all_transaction_user_grouping']);
Route::get('/get_all_transaction_admin_grouping',[transactiondetails::class,'get_all_transaction_admin_grouping']);
Route::get('/get_all_transaction_user_transaction',[transactiondetails::class,'get_all_transaction_user1_transaction']);
Route::get('/generate_pdf/{id}',[transactiondetails::class,'generate_pdf']);
Route::get('/generate_pdf_all/{ut_id}',[transactiondetails::class,'generate_pdf_all']);
Route::get('/generate_precreption/{id}',[transactiondetails::class,'generate_precreption']);




Route::post('/servicesdata/{id}', [ReceiptionistDashboard::class, 'getServices']);



// medicans
Route::post('/add-medicaine', [ReceiptionistDashboard::class, 'addMedicaine']);
Route::get('/medicaines', [ReceiptionistDashboard::class, 'getMedicaine']);
Route::get('/medicaine/{id}', [ReceiptionistDashboard::class, 'getMedicaineByid']);
Route::post('/medicaine/{id}/update', [ReceiptionistDashboard::class, 'updateMedicaine']);




// end of  19-11-2024


 Route::get('/get_user_appointment',[AppointmentController::class,'add_user_appointment']);
 Route::get('/get_all_appointment',[AppointmentController::class,'add_all_appointment']);
 Route::get('/get_all_appointment/{uid}',[AppointmentController::class,'add_serch_by_id_appointment']);
 Route::get('/search_by_op_no/{uid}',[AppointmentController::class,'search_by_op_no']);
//  Route::get('/search_by_op_date/{data}',[AppointmentController::class,'search_by_date']);
 Route::get('/search_by_op_date', [AppointmentController::class, 'search_by_date']);
  Route::get('/profile',[profileController::class,'profileController']);
  Route::put('/updateProfile',[profileController::class,'UpdateController']);
  Route::post('/updateProfilePicture',[profileController::class,'UpdateProfilePictureController']);
  //past appointments 
  Route::get('/past_appointment',[AppointmentController::class,'past_appointment']);
  Route::get('/future_appointment',[AppointmentController::class,'future_appointment']);


  // pet add edit delete
  Route::post('/add_pet',[MypetController::class,'add_pet_details']);
  Route::get('/get_my_pet',[MypetController::class,'get_my_pet_details']);
  Route::get('/get_my_pets/{id}',[MypetController::class,'get_my_pets']);
  Route::post('/get_my_pets/{id}/update',[MypetController::class,'update']);
  Route::post('/get_my_pets/{id}/updateAdmin',[MypetController::class,'updateAdmin']);
    

  
});
// Route::post('/password-reset/confirm', [AuthController::class, 'reset'])->name('password.reset');
// Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
