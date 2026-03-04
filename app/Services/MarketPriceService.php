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

            $entryDate = today()->format('Y-m-d');
            $userID    = Auth::user()->UserID;
            $data      = [];

            foreach ($request->products as $item) {
                $existing = WholeSaleMarketRate::where('ProductCode',  $item['ProductCode'])
                    ->where('LocationCode', $request->LocationCode)
                    ->where('CreatedBy',    $userID)
                    ->where('EntryDate',    $entryDate)
                    ->first();

                if ($existing) {
                    WholeSaleMarketRateLog::create([
                        'WSMRID'       => $existing->WSMRID,
                        'LocationCode' => $existing->LocationCode,
                        'ProductCode'  => $existing->ProductCode,
                        'CompanyPrice' => $existing->CompanyPrice,
                        'MarketPrice'  => $existing->MarketPrice,
                        'EntryDate'    => $existing->EntryDate,
                        'EntryAddress' => $existing->EntryAddress,
                        'Lat'          => $existing->Lat,
                        'Long'         => $existing->Long,
                        'CreatedBy'    => $userID,
                        'CreatedAt'    => Carbon::now()
                    ]);

                    $existing->update([
                        'MarketPrice' => $item['MarketPrice'],
                        'EntryDate'   => $entryDate,
                        'Lat'         => $request->Lat,
                        'Long'        => $request->Long,
                        'UpdatedBy'   => $userID,
                        'UpdatedAt'   => Carbon::now()
                    ]);

                    $data[] = $existing->fresh();
                } else {
                    $data[] = WholeSaleMarketRate::create([
                        'LocationCode' => $request->LocationCode,
                        'ProductCode'  => $item['ProductCode'],
                        'CompanyPrice' => $item['CompanyPrice'],
                        'MarketPrice'  => $item['MarketPrice'],
                        'EntryDate'    => $entryDate,
                        'EntryAddress' => $request->EntryAddress,
                        'Lat'          => $request->Lat,
                        'Long'         => $request->Long,
                        'CreatedBy'    => $userID,
                        'CreatedAt'    => Carbon::now()
                    ]);
                }
            }

            return $data;
    }

}