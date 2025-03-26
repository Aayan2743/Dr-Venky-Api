<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class clinictransaction extends Model
{
    use HasFactory;

    protected $guarded=[];

    public $table="clinictransactions";

    // public function payment_collect_by(){
    //     return $this->hasMany(user::class,'paid_by_id','id');
    // }

    public function payment_collect_by(){
        return $this->belongsTo(User::class,'paid_by_id','id');
    }

    public function user(){
        return $this->belongsTo(User::class,'uid','id');
    }

    public function pet(){
        return $this->belongsTo(mypet::class,'pid','id');
    }

}
