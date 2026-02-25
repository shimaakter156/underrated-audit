<?php

namespace App\Services;

use App\Models\Location;
use App\Models\UserLocation;

class UserService
{
    public static function Location()
    {
        return Location::where('Status','=','Y')->get();
    }
}