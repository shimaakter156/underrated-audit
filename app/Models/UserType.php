<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserType extends Model
{
    use HasFactory;

    protected $table = "UserType";
    public $timestamps = false;
    public $primaryKey = "UserTypeID";
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
