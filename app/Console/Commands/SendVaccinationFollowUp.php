<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\VaccinationSchedule;
use App\Http\CustomHelper;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SendVaccinationFollowUp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vaccination:followup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send vaccination follow-up reminders based on the schedule.';

    /**
     * Execute the console command.
     *
     * @return int
     */




//  public function handle()
//     {
//         $now = now();
//         Log::info('Follow-up command started at ' . $now);

//         // Fetch daily records
//         $dailyRecords = VaccinationSchedule::where('frequency', 'daily')
//             ->whereTime('time', $now->format('H:i:s'))
//             ->get();

//         // Fetch weekly records
//         $weeklyRecords = VaccinationSchedule::where('frequency', 'weekly')
//             ->where(DB::raw('DAYOFWEEK(CURRENT_DATE)'), DB::raw('DAYOFWEEK(`time`)')) // Match the weekday
//             ->whereTime('time', $now->format('H:i:s'))
//             ->get();
            
            
//         //     Log::info('weekly ' . $weeklyRecords);
            
//         // $weeklyRecords = VaccinationSchedule::where('frequency', 'weekly')
//         //     ->whereRaw('DAYOFWEEK(CAST(`time` AS DATE)) = ?', [$now->dayOfWeekIso])
//         //     ->whereTime('time', $now->format('H:i:s'))
//         //     ->get(); 
            
            
            
//               Log::info('iso weekly ' . $weeklyRecords);
  
//     //dd($weeklyRecords);        

//         // Merge daily and weekly records
//         $records = $dailyRecords->merge($weeklyRecords);
        
//           Log::info('iso daily ' . $records);

//         // Send messages
//         foreach ($records as $record) {
//             $this->sendFollowUpMessage($record->mobile);
//             Log::info("Follow-up message sent to: {$record->mobile}");
//         }

//         $this->info('Follow-up messages sent.');
//         Log::info('Follow-up command completed at ' . now());

//         return Command::SUCCESS;
//     }


public function handle()
{
    $now = now();
// dd($now->toDateString());
    // Daily
    $dailyRecords = VaccinationSchedule::where('frequency', 'daily')
        ->whereTime('time', $now->format('H:i:s'))
        ->where('status',1)  // active 
        ->get();

    // Weekly
    $weeklyRecords = VaccinationSchedule::where('frequency', 'weekly')
        ->whereRaw('DAYOFWEEK(CAST(`time` AS DATE)) = ?', [$now->dayOfWeekIso])
        ->whereTime('time', $now->format('H:i:s'))
           ->where('status',1)  // active 
        ->get();

    // Monthly
    $monthlyRecords = VaccinationSchedule::where('frequency', 'monthly')
        ->whereDay('time', $now->day)
        ->whereTime('time', $now->format('H:i:s'))
           ->where('status',1)  // active 
        ->get();

    // Quarterly
    $quarterlyRecords = VaccinationSchedule::where('frequency', 'quarterly')
        ->whereIn(DB::raw('MONTH(`time`)'), [1, 4, 7, 10])
        ->whereDay('time', $now->day)
        ->whereTime('time', $now->format('H:i:s'))
           ->where('status',1)  // active 
        ->get();

    // Half-Yearly
    $halfYearlyRecords = VaccinationSchedule::where('frequency', 'half-yearly')
        ->whereIn(DB::raw('MONTH(`time`)'), [1, 7])
        ->whereDay('time', $now->day)
        ->whereTime('time', $now->format('H:i:s'))
           ->where('status',1)  // active 
        ->get();

    // Yearly
    $yearlyRecords = VaccinationSchedule::where('frequency', 'yearly')
        ->whereMonth('time', $now->month)
        ->whereDay('time', $now->day)
        ->whereTime('time', $now->format('H:i:s'))
           ->where('status',1)  // active 
        ->get();
        
        //custom

    // Custom One-Time Date (This could be a field like 'custom_date' on your table)
    $customDateRecords = VaccinationSchedule::where('frequency', 'custom')->where('custom_date', $now->toDateString())
        ->whereTime('time', $now->format('H:i:s'))
           ->where('status',1)  // active 
        ->get();
        
         Log::info("test: ".$now->toDateString());

    // Combine all records
    $allRecords = $dailyRecords->merge($weeklyRecords)
        ->merge($monthlyRecords)
        ->merge($quarterlyRecords)
        ->merge($halfYearlyRecords)
        ->merge($yearlyRecords)
        ->merge($customDateRecords);
$this->info('all.'. $allRecords);
    // Send messages
    foreach ($allRecords as $record) {
        // Format the custom date based on your needs
        $dogName=$record->pet_name;
        $petOwner=$record->pet_owner_name;
        $reason=$record->reason;
        $app_time=$record->app_time;
      
        // $messageDate = \Carbon\Carbon::parse($record->custom_date ?? $now->format('Y-m-d H:i:s'))->format('jS F Y \a\t h:i A');
        $messageDate = \Carbon\Carbon::parse($record->custom_date ?? $now->format('Y-m-d'))->format('jS F Y');
       
        $app_time_fixed = \Carbon\Carbon::parse($record->app_time)->format('h:i A');

      
        $message = "Your pet {$dogName} is scheduled for {$reason} on {$messageDate} at " . $now->format('H:i');
        
        $imgPath="https://demo.drvenkysanimalhospital.com/assets/logo1-BHrfIYPt.png";
       $message1= "Dear {$petOwner},

            This is a reminder that your beloved pet {$dogName} is scheduled for a {$reason} at Dr. Venkypet Clinic. The appointment is set for {$messageDate} at {$app_time_fixed}. Please ensure to arrive a few minutes early.
            
            If you need to reschedule or have any questions, feel free to contact us.
            
            Looking forward to seeing you!
            
            Warm regards,
            Dr. Venkypet Clinic";
        
        $this->sendFollowUpMessage($record->mobile, $message1,$imgPath);
        Log::info("Follow-up message sent to: {$record->mobile} for date: {$messageDate}");
    }

    $this->info('Follow-up messages sent.' );
}

private function sendFollowUpMessage($mobile, $message,$imgPath)
{
    // Assuming CustomHelper::follow_up sends the message
    CustomHelper::follow_up($mobile, $message,$imgPath);
}



    /**
     * Send follow-up message.
     *
     * @param string $mobile
     * @return void
     */
 



    // private function sendFollowUpMessage($mobile)
    // {
    //     // Assuming CustomHelper::follow_up sends the message
    //     CustomHelper::follow_up($mobile);
    // }
}
