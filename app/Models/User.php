<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = "UserManager";
    public $timestamps = false;
    public $primaryKey = 'UserID';
    protected $guarded = [];
    public $incrementing = false;
    protected $keyType = "string";

    protected $hidden = [
        'Password',
    ];
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function userType(){
        return $this->hasOne(UserType::class,'UserTypeID','UserTypeID');
    }





    public function userSubmenu()
    {
        return $this->hasMany(SubMenuPermission::class,'UserID','StaffID');
    }

    public function getAuthPassword()
    {
        return $this->Password;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

}
