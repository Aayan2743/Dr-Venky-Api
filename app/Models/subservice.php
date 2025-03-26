<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class subservice extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function service_details(){
        $this->hasMany(service::class,'service_id','id');
    }
    

        public function service()
        {
            return $this->belongsTo(service::class, 'service_id','id');
        }
   
}
