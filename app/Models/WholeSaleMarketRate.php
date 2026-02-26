<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WholeSaleMarketRate extends Model
{
    use HasFactory;
    protected $table = "WholeSaleMarketRate";
    public $timestamps = false;
    public $primaryKey = "WSMRID";
    public $incrementing = false;
    protected $keyType = "string";
    protected $guarded = [];
}
