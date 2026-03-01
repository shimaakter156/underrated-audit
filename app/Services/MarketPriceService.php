<?php

namespace App\Services;

use App\Models\WholeSaleMarketRate;
use App\Models\WholeSaleMarketRateLog;
use App\Traits\APIResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MarketPriceService
{
    use APIResponseTrait;

    public function getMarketPriceList($userID){
        $data = WholeSaleMarketRate::join('UserManager as um ','um.UserID','=','WholeSaleMarketRate.CreatedBy')
            ->join('Product as p','p.ProductCode','=','WholeSaleMarketRate.ProductCode')
            ->join('Location as l ','l.LocationCode','=','WholeSaleMarketRate.LocationCode')
            ->where('um.UserTypeID','=',3)
            ->select('WholeSaleMarketRate.*','p.ProductName','l.LocationName','um.Name')
            ->where('WholeSaleMarketRate.CreatedBy','=',$userID)
            ->orderBy('CreatedAt','desc')
            ->get();
        return $this->successResponse($data,'');

    }
    public function store($request)
    {
        try {
            $query = WholeSaleMarketRate::where('ProductCode','=',$request->ProductCode)
                ->where('LocationCode','=',$request->LocationCode)
                ->where('CreatedBy','=',Auth::user()->UserID)
                ->where('EntryDate','=',today());
            $dataExist = $query->first();
            if ($dataExist) {
                WholeSaleMarketRateLog::create([
                    'WSMRID'       => $dataExist->WSMRID,
                    'LocationCode' => $dataExist->LocationCode,
                    'ProductCode'  => $dataExist->ProductCode,
                    'CompanyPrice' => $dataExist->CompanyPrice,
                    'MarketPrice'  => $dataExist->MarketPrice,
                    'EntryDate'    => $dataExist->EntryDate,
                    'EntryAddress' => $dataExist->EntryAddress,
                    'Lat'          => $dataExist->Lat,
                    'Long'         => $dataExist->Long,
                    'CreatedBy'    => Auth::user()->UserID,
                    'CreatedAt'    => Carbon::now()
                ]);

                $query->update([
                    'MarketPrice' => $request->MarketPrice,
                    'EntryDate'   => $request->EntryDate,
                    'Lat'         => $request->Lat,
                    'Long'        => $request->Long,
                    'UpdatedBy'   => Auth::user()->UserID,
                    'UpdatedAt'   => Carbon::now()
                ]);

                $data = $query->first();
            }else{
                $data =  WholeSaleMarketRate::create([
                    'LocationCode' => $request->LocationCode,
                    'ProductCode' => $request->ProductCode,
                    'CompanyPrice' => $request->CompanyPrice,
                    'MarketPrice' => $request->MarketPrice,
                    'EntryDate' =>$request->EntryDate,
                    'EntryAddress' => $request->EntryAddress,
                    'Lat' => $request->Lat,
                    'Long' => $request->Long,
                    'CreatedBy' => Auth::user()->UserID,
                    'CreatedAt' => Carbon::now()
                ]);

            }

        return $data;
        } catch (\Exception $exception) {
            return $this->errorResponse($exception->getMessage());
        }
    }
}