<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Services\BusinessService;
use App\Services\DepartmentService;
use App\Services\RoleService;

class CommonController extends Controller
{
    public function userModalData() {
        return response()->json([
            'status' => 'success',
            'roles' => RoleService::list(),
            'allSubMenus' => Menu::whereNotIn('MenuID',['Dashboard','Users'])->with('allSubMenus')->orderBy('MenuOrder','asc')->get()
        ]);
    }

    public function encode($param) {

    }

    public function decode($param) {

    }
}
