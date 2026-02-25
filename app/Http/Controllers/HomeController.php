<?php

namespace App\Http\Controllers;

use App\Models\Depot;
use App\Models\UserOutlet;
use App\Services\DeviceInfo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    public function index()
    {
        return view('welcome');
    }

}
