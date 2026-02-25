<?php

namespace App\Services;

use App\Models\Location;
use App\Models\UserLocation;

class LocationService
{
    public static function location()
    {
        return Location::where('Status','=','Y')->get();
    }

    public static function userLocation()
    {
        $data = UserLocation::select('Userlocation.*','m.Name')->join('UserManager as m','m.UserID','=','UserLocation.UserID')->get();
        return $data;
    }


}