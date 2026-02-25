<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    use HasFactory;
    protected $connection = "sqlsrv_web_menu";
    protected $table = "MenuItem";
    public $primaryKey = 'ID';
    protected $guarded = [];
    public $timestamps = false;
}
