<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Menu extends Model
{
    use HasFactory;
    protected $table = "Menus";
    public $primaryKey = 'MenuID';
    protected $guarded = [];
    public $timestamps = false;
    protected $keyType = 'string';

    public function subMenus() {
        return $this->hasMany(SubMenu::class,'MenuID','MenuID')
            ->join('SubMenuPermission','SubMenuPermission.SubMenuID','SubMenus.SubMenuID')
            ->where('UserID',Auth::user()->StaffID)->where('Status',1)->orderBy('SubMenuOrder');
    }

    public function allSubMenus() {
        return $this->hasMany(SubMenu::class,'MenuID','MenuID')->where('Status',1);
    }
}
