<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Menu;
use App\Models\UserMenu;
use Illuminate\Http\Request;

class MenuPermissionController extends Controller
{
    public function getUserMenuPermission($id)
    {
        $data['menu'] = Menu::select('ID', 'Name')->with(['MenuItem' => function ($item) {
            $item->select('ID', 'MenuId', 'Name');
        }])->orderBy('order', 'asc')->get();
        $data['usermenu'] = UserMenu::where('UserId', $id)->pluck('MenuItemId');
        return $data;
    }

    public function saveUserMenuPermission(Request $request)
    {
        $userId = $request->userId;
        $permission = $request->permission;
        $sortedPerm = [];
        foreach ($permission as $key => $value) {
            if ($value) array_push($sortedPerm, $key);
        }

        $current = UserMenu::where('UserId', $userId)->pluck('MenuItemId')->toArray();
        $inserted = array_diff($sortedPerm, $current);
        foreach ($inserted as $item) {
            UserMenu::create(['UserId' => $userId, 'MenuItemId' => $item]);
        }
        $remove = array_diff($current, $sortedPerm);
        UserMenu::where('UserId', $userId)->whereIn('MenuItemId', $remove)->delete();

        return response()->json(['message' => "Menu permissions updated Successfully"]);
    }
}
