<?php

namespace App\Services;

use App\Models\EmailJob;
use App\Models\Product;
use App\Traits\APIResponseTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductService
{
    use APIResponseTrait;
    public function product()
    {
        try {
          $data = Product::where('Status','=','Y')->get();

           return $this->successResponse($data,'');

        } catch (\Exception $exception) {
        }
    }
}