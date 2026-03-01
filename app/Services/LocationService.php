<?php

namespace App\Services;

use App\Models\Location;
use App\Models\UserLocation;
use App\Traits\APIResponseTrait;

class LocationService
{
    use APIResponseTrait;
    public function location()
    {
        $data = Location::where('Status','=','Y')->get();
        return $this->successResponse($data,'');
    }

    public function userLocation($userID)
    {

        $data = UserLocation::select('Userlocation.*','m.Name','l.LocationName','l.LocationShortName')
            ->join('UserManager as m','m.UserID','=','UserLocation.UserID')
            ->join('Location as l ','l.LocationCode','=','UserLocation.LocationCode')
            ->where('UserLocation.UserID','=',$userID)
            ->where('l.Status','=','Y')
            ->get();

        if ($data->isNotEmpty()){
            return $this->successResponse($data,'');
        }else{
            return $this->errorResponse('No data found!');

        }

    }


}