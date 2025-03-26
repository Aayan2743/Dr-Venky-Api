<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Support\Facades\Storage;


class User extends Authenticatable implements JWTSubject

{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'phone',
        'designation',
        'user_type',
        'mypets',
        'active_status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier(){
        return $this->getKey();
     }
     
     public function getJWTCustomClaims(){
        return [];
     }


     public function appointmentdetails(){
        return $this->hasMany(appointment::class,'user_id','id');
     }

     public function getProfilePictureUrlAttribute()
    {

        $default_path="profile_pictures\avatar.jpg";
        // Check if a profile picture exists
        return $this->profile_picture ? url(Storage::url($this->profile_picture)) : url(Storage::url($default_path));;

        // return $this->profile_picture
        // ? url(Storage::url($this->profile_picture))
        // : url('/path/to/default-image.jpg');
    }

    // Ensure `profile_picture_url` is included in the serialized model
    protected $appends = ['profile_picture_url'];


    public function payment_collect(){
        return $this->belongsTo(clinictransaction::class,'paid_by_id','id');
    }

    
    public function getUserTypeNameAttribute()
{
    $types = [
        0 => 'Admin',
        1 => 'Customer',
        2 => 'Doctor',
        3 => 'Receiptionist',
        4 => 'Lab',
        5 => 'POS OPERATOR',


        // 0 for admin, 1 for customer, 2 doctor ,3 for recep, 4 lab ,5 POS OPERATOR
    ];

    return $types[$this->user_type] ?? 'Unknown';
}


public function prescriptions_user()
{
    return $this->hasMany(prescription::class, 'uid', 'id'); // Adjust 'pid' and 'id' as necessary
}

     
}
