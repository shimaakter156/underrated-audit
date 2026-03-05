<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserStoreRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Models\Advances;
use App\Models\Banks;
use App\Models\Branch;
use App\Models\Menu;
use App\Models\SubMenuPermission;
use App\Models\User;
use App\Models\UserBusiness;
use App\Models\UserDepartment;
use App\Services\BusinessService;
use App\Services\DepartmentService;
use App\Services\HelperService;
use App\Services\UserService;
use App\Traits\APIResponseTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{

    protected $userService;
    use APIResponseTrait;
    public function __construct(UserService $userService){
        $this->userService = $userService;
    }

    public function index(Request $request)
    {
       return $this->userService->userIndex($request);
    }
    public function store(UserStoreRequest $request)
    {
        try {

            $staffID =$request->staffId;
            $userTypeID = $request->userType['UserTypeID'];
            $location = $request->location;
            DB::beginTransaction();
            $this->userService->userExist($staffID);
            $newUserID = $this->userService->generateUserID($userTypeID,$staffID);

           $data = $this->userService->storeUser($request,$newUserID,$userTypeID);
            if (!empty($location)){
              $this->userService->insertUserLocation($location,$newUserID);
            }
            DB::commit();
            return $this->successResponseWeb($data,'User Created Successfully',201);
        } catch (\Exception $exception) {
            DB::rollBack();
            return $this->errorResponseWeb($exception->getMessage() . '-' . $exception->getLine(),500);
        }


    }

    public function update(UserUpdateRequest $request)
    {
        try {
            DB::beginTransaction();
            $user = User::find($request->staffId);
            $user->Email = $request->email;
            $user->Mobile = $request->mobile;
            $user->Status = $request->status;
            $user->RoleID = $request->userType['RoleID'];
            $user->UpdatedBy = Auth::user()->StaffID;
            $user->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $user->save();
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User Updated Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    public function updatePassword(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'staffId' => 'required|string',
                'password' => 'required|string'
            ]);
            if ($validator->fails()) {
                return response()->json(['message' => $validator->errors()], 400);
            }
            $user = User::find($request->staffId);
            $user->Password = bcrypt($request->password);
            $user->save();
            return response()->json([
                'status' => 'success',
                'message' => 'Password Updated Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], 500);
        }
    }
    public function passwordChange(Request $request)
    {
        if($request->updatePassword ===$request->confirmUpdatePassword){
            try{
                $validator = Validator::make($request->all(), [
                    'userId' => 'required|string',
                    'updatePassword' => 'required|string',
                    'confirmUpdatePassword' => 'required|string',
                ]);
                $user = User::find($request->userId);
                $user->Password = bcrypt($request->updatePassword);
                $user->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Password Updated Successfully'
                ]);
            }
            catch (\Exception $exception){
                return response()->json([
                    'status' => 'error',
                    'message' => $exception->getMessage()
                ], 500);
            }
        }
        else{
            return response()->json(['message' => 'Password did not match'], 400);
        }

    }

    public function delete($id)
    {
        if (false) {
            return response()->json(['message' => "User is already used!"], 500);
        } else {
            User::where('id', $id)->delete();
            return response()->json(['message' => "User deleted successfully"]);
        }
    }

    public function getUserInfo($staffId)
    {
        $user = User::where('StaffID', $staffId)->with(['userType','userSubmenu'])->first();
        $allSubMenus = Menu::whereNotIn('MenuID', ['Dashboard', 'Users'])->with('allSubMenus')->orderBy('MenuOrder', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'allSubMenus' => $allSubMenus
        ]);
    }


}
