<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class appointment extends Model
{
    use HasFactory;

    protected $guarded=[];

 

    public function pet_details()
    {
        return $this->belongsTo(mypet::class, 'pet_id', 'id');
    }
    
     public function user_details()
    {
        return $this->belongsTo(user::class, 'user_id', 'id');
    }

    public function doctor_details()
    {
        return $this->belongsTo(user::class, 'dr_id', 'id');
    }

    public function getStatusTextAttribute()
    {
        $statuses = [
            0 => 'Booked',
            1 => 'Confirmed',
            2 => 'Reports',
            // Add more statuses as needed
        ];

        return $statuses[$this->status] ?? 'Unknown'; // Default to "Unknown" if status is not defined
    }



    public function prescriptions()
    {
        return $this->hasMany(prescription::class, 'aaid', 'id'); // 'appointment_id' is the foreign key in the prescriptions table
    }



    public function labReports()
{
    return $this->hasMany(labreport::class, 'aid'); // Foreign key in lab_reports table
}




}
