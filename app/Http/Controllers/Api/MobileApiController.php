<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\LocationService;
use App\Services\ProductService;

class MobileApiController extends Controller
{
    protected $locationService, $productService;
    public function __construct(LocationService $locationService ,ProductService $productService){
        $this->locationService = $locationService;
        $this->productService = $productService;
    }



    public function getLocation(){
        return $this->locationService->location();

    }
    public function getUserLocation(){
        return $this->locationService->userLocation();

    }

    public function getProduct(){
        return $this->productService->product();
    }

}
