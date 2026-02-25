<?php

namespace App\Services;

use App\Models\EmailJob;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProductService
{
    public static function product()
    {
        try {
          $data = Product::where('Status','=','Y')->get();
          return $data;
        } catch (\Exception $exception) {
        }
    }
}