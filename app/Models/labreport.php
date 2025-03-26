<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class labreport extends Model
{
    use HasFactory;

    // protected $guraded=[];

    protected $appends = ['full_path'];

    // Accessor for full_path
    public function getFullPathAttribute()
    {
        // Assuming files are stored in `storage/app/public`

        if($this->filepath){
            return asset( $this->filepath);
        }

        return null;
        
    }

    protected $table = 'labreports';
    // protected $fillable = ['aid', 'pid', 'uid','ssid','statusid','updated_id','filepath']; 
    protected $fillable = ['aid', 'pid', 'uid', 'ssid', 'statusid', 'updated_id'];


    public $timestamps=false;

    public function appiontment(){
        return $this->belongsTo(appointment::class,'aid','id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'uid', 'id'); // 'uid' is the foreign key in prescriptions, 'id' is the primary key in users
    }

    public function pet()
    {
        return $this->belongsTo(mypet::class, 'pid', 'id'); // Adjust 'pid' and 'id' as necessary
    }


    public function getAllSubservices()
    {
        return $this->belongsTo(subservice::class,'ssid','id');

    }

    public function appointment()
        {
            return $this->belongsTo(appointment::class, 'aid'); // Foreign key in lab_reports table
        }



}
