<?php

namespace App\Services;

use App\Models\SubMenuPermission;
use App\Models\User;
use App\Traits\APIResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserService
{
    use  APIResponseTrait;


    public function storeUser(Request $request,$newUserID,$userTypeID){

        dd($newUserID,$userTypeID);
        DB::beginTransaction();
        $user = new User();
        $user->UserID = $newUserID;
        $user->StaffID = $request->staffId;
        $user->Name = $request->staffName;
        $user->Email = $request->email;
        $user->PhoneNo = $request->mobile;
        $user->Password = bcrypt($request->password);
        $user->Status = $request->status;
        $user->UserTypeID = $userTypeID;
        $user->CreatedBy = Auth::user()->UserID;
        $user->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
        $user->save();
        $submenus = [];
        foreach ($request->selectedSubMenu as $row) {
            $submenus[] = [
                'UserID' => $newUserID,
                'SubMenuID' => $row
            ];
        }
        SubMenuPermission::insert($submenus);
        DB::commit();
        return $this->successResponseWeb(null,'User Created Successfully',201);

    }
    public function userExist($userID){
        if (User::where('UserID', $userID)->exists()) {
            return $this->errorResponseWeb('User already exists.',409);
        }
    }

    public function generateUserID($userTypeID,$staffID){
        if ($userTypeID===3){
            $user =  User::where('UserTypeID','=',$userTypeID)->orderby('UserID','DESC')->first();
            $prev = $user->UserID;
            $nextUserID = 'SR'.(explode('SR',$prev)[1]+1);
        }else{
            $nextUserID = $staffID;
        }

        return $nextUserID;
    }

    public function userIndex(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $query = User::join('UserType', 'UserType.UserTypeID', 'UserManager.UserTypeID')
            ->leftjoin('UserLocation','UserLocation.UserID','=','UserManager.UserID')
            ->leftjoin('Location','Location.LocationCode','=','UserLocation.LocationCode')
                -> where(function ($q) use ($search) {
                $q->where('Name', 'like', '%' . $search . '%');
                $q->orWhere('StaffID', 'like', '%' . $search . '%');
                $q->orWhere('Email', 'like', '%' . $search . '%');
                $q->orWhere('PhoneNo', 'like', '%' . $search . '%');
            })
            ->where('UserManager.UserTypeID', '!=', '1')
            ->orderBy('StaffID', 'desc')
            ->select(
                'UserManager.*',
                'UserType.UserTypeName',
                'Location.LocationName'
            );

        if ($request->type === 'export') {
            return response()->json([
                'data' => $query->get()
            ]);
        }
        return $query->paginate($take);
    }
}