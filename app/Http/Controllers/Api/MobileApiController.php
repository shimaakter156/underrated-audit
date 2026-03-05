<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreMarketPriceRequest;
use App\Services\LocationService;
use App\Services\MarketPriceService;
use App\Services\ProductService;
use App\Traits\APIResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class MobileApiController extends Controller
{
    use APIResponseTrait;

    protected $locationService, $productService, $marketPriceService;
    public function __construct(LocationService $locationService ,ProductService $productService, MarketPriceService $marketPriceService){
        $this->locationService = $locationService;
        $this->productService = $productService;
        $this->marketPriceService = $marketPriceService;
    }
    public function getMarketPrice(Request $request){

        $userID = Auth::user()->UserID;
        $from = $request->fromDate;
        $to = $request->toDate;
        $location = $request->locationCode;
        $data =[
            'userID'=>$userID,
            'fromDate'=>$from,
            'toDate'=>$to,
            'location'=>$location
        ];
        $service = $this->marketPriceService->getMarketPriceList($data);
        return $service;
    }
    public function store(StoreMarketPriceRequest $request){
        try{
            $data =  $this->marketPriceService->store($request);
            return $this->successResponse($data,'Successfully Stored!');
         } catch (\Exception $exception) {
         return $this->errorResponse($exception->getMessage());
        }
    }




    public function getLocation(){
        return $this->locationService->location();

    }
    public function getUserLocation($userID){
        return $this->locationService->userLocation($userID);

    }

    public function getProduct(){
        return $this->productService->product();
    }

}
