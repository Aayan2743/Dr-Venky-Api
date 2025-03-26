<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class medicanprecreprion extends Model
{
    use HasFactory;
    
      protected $guarded=[];
     
      public $timestamps = false;
      
       public function prescription()
    {
        return $this->belongsTo(prescription::class, 'id', 'id');
    }
}
