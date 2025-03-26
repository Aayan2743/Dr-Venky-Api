<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class service extends Model
{
    use HasFactory;

    protected $guarded=[];

    public function subservicedetails(){
        return $this->hasMany(subservice::class,'service_id','id');
    }
}
