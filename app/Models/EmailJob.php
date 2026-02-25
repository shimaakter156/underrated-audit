<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmailJob extends Model
{
    use HasFactory;

    protected $table = "EmailJob";
    public $timestamps = false;
    protected $guarded = [];
}
