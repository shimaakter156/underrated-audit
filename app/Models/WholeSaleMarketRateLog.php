<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholeSaleMarketRateLog extends Model
{
    use HasFactory;
    protected $table = "WholeSaleMarketRateLog";
    public $timestamps = false;
    public $primaryKey = "WSMRLogID";
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
