<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Psy\CodeCleaner\FunctionReturnInWriteContextPass;

class mypet extends Model
{
    use HasFactory;

    protected $guarded=[];


    public function appointments()
    {
        return $this->hasMany(appointment::class, 'pet_id', 'id');
    }

    public function prescriptions()
    {
        return $this->hasMany(prescription::class, 'pid', 'id'); // Adjust 'pid' and 'id' as necessary
    }


 


   
}
