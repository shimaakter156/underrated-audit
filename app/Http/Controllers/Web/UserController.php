<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
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
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    public function checkUser(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'value' => 'required|string',
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => 'Invalid'], 400);
        }
        try {
            $check = User::where('UserID', $request->value)->exists();
            return response()->json(['status' => $check], 200);
        } catch (\Exception $exception) {
            return response()->json(['message' => 'Oops! Something went wrong'], 400);
        }
    }

    public function allUsers()
    {
        $user = JWTAuth::parseToken()->authenticate();
        if ($user->UserType == 'admin') {
            return User::select('ID', 'Name', 'Designation')->get();
        } else {
            return User::where('UserType', '!=', 'admin')
                ->select('ID', 'Name', 'Designation')
                ->get();
        }
    }

    public function index(Request $request)
    {
        $take = $request->take;
        $search = $request->search;
        $query = User::join('UserType', 'UserType.UserTypeID', 'UserManager.UserTypeID')
            ->where(function ($q) use ($search) {
                $q->where('Name', 'like', '%' . $search . '%');
                $q->orWhere('StaffID', 'like', '%' . $search . '%');
                $q->orWhere('Email', 'like', '%' . $search . '%');
                $q->orWhere('PhoneNo', 'like', '%' . $search . '%');
            })
            ->where('UserManager.UserTypeID', '=', '3')
            ->orderBy('Name', 'asc');
        if ($request->type === 'export') {
            return response()->json([
                'data' => $query->get()
            ]);
        }
        return $query->paginate($take);
    }
    //Branch List
    public function branchList(Request $request){
        $take = $request->take;
        $search = $request->search;
        return Branch::join('Banks', 'Banks.BankID', 'Branches.BankID')
            ->where(function ($q) use ($search) {
                $q->where('Banks.BankName', 'like', '%' . $search . '%')
                ->orWhere('Branches.BranchName', 'like', '%' . $search . '%');
            })
            ->orderBy('Id', 'desc')
            ->select('Banks.BankID','Banks.BankName','Branches.BranchName','Branches.RoutingNumber','Branches.Id')
            ->paginate($take);
    }
    public function getBanks(){
        try {
            $banks = Banks::all();
            return response()->json([
                'banks' => $banks
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    //save Branch

    public function saveBranch(Request $request){
        try {
          if($request->BranchName && $request->RoutingNumber){
              $branch = new Branch();
              $branch->BankID= $request->BankID;
              $branch->BranchName= $request->BranchName;
              $branch->RoutingNumber= $request->RoutingNumber;
              $branch->save();
              return response()->json([
                      'status' => 'success',
                      'message' => 'Branch Added Successfully'
                  ]);
          }
          else{
              return response()->json(['message' => 'Branch name and Routing Number are required'], 400);
          }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    //
    public function updateBranch(Request $request){

        try {
            if($request->BranchName && $request->RoutingNumber){
                $branch = Branch::where('Id',$request->Id)->first();
                $branch->BankID= $request->BankID;
                $branch->BranchName= $request->BranchName;
                $branch->RoutingNumber= $request->RoutingNumber;
                $branch->save();
                return response()->json([
                    'status' => 'success',
                    'message' => 'Branch Updated Successfully'
                ]);
            }
            else{
                return response()->json(['message' => 'Branch name and Routing Number are required'], 400);
            }
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }
    public function getCorrespondingBranchAndRoute(Request $request){
        try {
            if($request->step ===1){
                $correspondingBranches = Branch::where('BankID',$request->BankID)->get();
                return response()->json([
                    'branch_options' => $correspondingBranches
                ]);
            }
            else{
                $correspondingBranches = Branch::where('Id',$request->BranchId)->first();

                if($correspondingBranches){
                    $RoutingNumber = $correspondingBranches->RoutingNumber;
                    return response()->json([
                        'RoutingNumber' => $RoutingNumber
                    ]);
                }
            }
        }
        catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 400);
        }
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'staffId' => 'required|string',
            'staffName' => 'required|string',
            'designation' => 'required',
            'deptCode' => 'required',
            'business' => 'required',
            'department' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'userType' => 'required',
            'status' => 'required',
            'password' => 'required|string|min:6'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        try {
            if (User::where('StaffID', $request->staffId)->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'This staff is already exists in AMS Database.'
                ], 400);
            }
            DB::beginTransaction();
            $user = new User();
            $user->StaffID = $request->staffId;
            $user->StaffName = $request->staffName;
            $user->Designation = $request->designation;
            $user->Business = $request->business;
            $user->Department = $request->department;
            $user->DeptCode = $request->deptCode;
            $user->Email = $request->email;
            $user->Mobile = $request->mobile;
            $user->UserID = $request->staffId;
            $user->Password = bcrypt($request->password);
            $user->Status = $request->status;
            $user->RoleID = $request->userType['RoleID'];
            $user->CreatedBy = Auth::user()->StaffID;
            $user->UpdatedBy = Auth::user()->StaffID;
            $user->CreatedAt = Carbon::now()->format('Y-m-d H:i:s');
            $user->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            if (!empty($request->signature)) {
                $helper = new HelperService();
                $fileName = $helper::imageUpload($request->signature, 'signature-', public_path('uploads/'));
                $user->Signature = $fileName;
            }
            if (in_array('MyCheckList',$request->selectedSubMenu)) {
                $user->IsChecker = 'Y';
            }
            if (in_array('MyApprovals',$request->selectedSubMenu)) {
                $user->IsApprover = 'Y';
            }
            if (in_array('MyTourCheckList',$request->selectedSubMenu)) {
                $user->IsTourChecker = 'Y';
            }
            if (in_array('MyTourApprovals',$request->selectedSubMenu)) {
                $user->IsTourApprover = 'Y';
            }
            $user->save();
            //User Business Insert
            if ($request->allowedBusiness !== null) {
                $businesses = [];
                if ($request->allowedBusiness[0]['Business'] === 'All') {
                    $request->allowedBusiness = BusinessService::list();
                }
                foreach ($request->allowedBusiness as $row) {
                    $businesses[] = [
                        'StaffID' => $request->staffId,
                        'BusinessID' => $row['Business']
                    ];
                }
                UserBusiness::insert($businesses);
            }
            //User Department Insert
            if ($request->allowedDepartment !== null) {
                $departments = [];
                if ($request->allowedDepartment[0]['DeptCode'] === 'All') {
                    $request->allowedBusiness = DepartmentService::list();
                }
                foreach ($request->allowedDepartment as $row) {
                    $departments[] = [
                        'StaffID' => $request->staffId,
                        'DepartmentID' => $row['DeptCode']
                    ];
                }
                UserDepartment::insert($departments);
            }
            $submenus = [];
            foreach ($request->selectedSubMenu as $row) {
                $submenus[] = [
                    'UserID' => $request->staffId,
                    'SubMenuID' => $row
                ];
            }
            SubMenuPermission::insert($submenus);
            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'User Created Successfully'
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage() . '-' . $exception->getLine()
            ], 500);
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staffId' => 'required|string',
            'staffName' => 'required|string',
            'designation' => 'required',
            'business' => 'required',
            'department' => 'required',
            'email' => 'required',
            'mobile' => 'required',
            'userType' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['message' => $validator->errors()], 400);
        }
        try {
            DB::beginTransaction();
            $user = User::find($request->staffId);
            $user->Designation = $request->designation;
            $user->Business = $request->business;
            $user->Email = $request->email;
            $user->Mobile = $request->mobile;
            $user->Status = $request->status;
            $user->RoleID = $request->userType['RoleID'];
            $user->UpdatedBy = Auth::user()->StaffID;
            $user->UpdatedAt = Carbon::now()->format('Y-m-d H:i:s');
            if (!empty($request->signature)) {
                if (!empty($user->Signature)) {
                    if (file_exists(public_path('uploads/'.$user->Signature))) {
                        unlink(public_path('uploads/'.$user->Signature));
                    }
                }
                $helper = new HelperService();
                $fileName = $helper::imageUpload($request->signature, 'signature-', public_path('uploads/'));
                $user->Signature = $fileName;
            }
            if (in_array('MyCheckList',$request->selectedSubMenu)) {
                $user->IsChecker = 'Y';
            }
            if (in_array('MyApprovals',$request->selectedSubMenu)) {
                $user->IsApprover = 'Y';
            }
            if (in_array('MyTourCheckList',$request->selectedSubMenu)) {
                $user->IsTourChecker = 'Y';
            }
            if (in_array('MyTourApprovals',$request->selectedSubMenu)) {
                $user->IsTourApprover = 'Y';
            }
            $user->save();
            //USER BUSINESS DELETE
            UserBusiness::where('staffID', $request->staffId)->delete();
            //USER DEPARTMENT DELETE
            UserDepartment::where('staffID', $request->staffId)->delete();
            //submenu permission delete
            SubMenuPermission::where('UserID', $request->staffId)->delete();
            //User Business Insert
            if ($request->allowedBusiness !== null && $request->userType['RoleID'] === 'RepresentativeUser') {
                $businesses = [];
                foreach ($request->allowedBusiness as $row) {
                    $businesses[] = [
                        'StaffID' => $request->staffId,
                        'BusinessID' => $row['Business']
                    ];
                }
                UserBusiness::insert($businesses);
            }
            //User Department Insert
            if ($request->allowedDepartment !== null && $request->userType['RoleID'] === 'RepresentativeUser') {
                $departments = [];
                foreach ($request->allowedDepartment as $row) {
                    $departments[] = [
                        'StaffID' => $request->staffId,
                        'DepartmentID' => $row['DeptCode']
                    ];
                }
                UserDepartment::insert($departments);
            }
            $submenus = [];
            foreach ($request->selectedSubMenu as $row) {
                $submenus[] = [
                    'UserID' => $request->staffId,
                    'SubMenuID' => $row
                ];
            }
            SubMenuPermission::insert($submenus);
            //UPDATE ADVANCE CONTACTS
            Advances::where('ResStaffID',$request->staffId)->update([
                'ResStaffEmail' => $request->email,
                'ResStaffMobile' => $request->mobile
            ]);
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
        $user = User::where('StaffID', $staffId)->with(['roles', 'userBusiness.business', 'userDepartment.department', 'business', 'userSubmenu'])->first();
        $allSubMenus = Menu::whereNotIn('MenuID', ['Dashboard', 'Users'])->with('allSubMenus')->orderBy('MenuOrder', 'asc')->get();
        return response()->json([
            'status' => 'success',
            'data' => $user,
            'allSubMenus' => $allSubMenus
        ]);
    }

    public function hrData(Request $request)
    {
        $query = DB::connection('hr_db')->select("SELECT P.EmpCode, P.Name, D.DesgName, De.DeptName, E.DeptCode
        FROM Personal P	
            INNER JOIN Employer E
                ON P.EmpCode = E.EmpCode
            INNER JOIN Designation D
                ON E.DesgCode = D.DesgCode
            INNER JOIN Department DE
                ON E.DeptCode = DE.DeptCode
        WHERE E.EmpCode = '$request->staffId'");
        if (count($query)) {
            $data = $query[0];
        } else {
            $data = [];
        }
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    public function loadUsersHR(Request $request)
    {
        $auth = Auth::user();
        if ($auth->RoleID === 'RepresentativeUser') {
            $userBusiness = UserBusiness::where('StaffID',$auth->StaffID)->get()->pluck('BusinessID')->toArray();
            $userDepartments = UserDepartment::where('StaffID',$auth->StaffID)->get()->pluck('DepartmentID')->toArray();
            if (count($userDepartments) > 0 && count($userBusiness)) {
                $query = User::select('StaffID as EmpCode','StaffName as Name','des.DesgName','Department as DeptName', 'Business', 'Email', 'Mobile','Advances.AccountNo','Advances.BankID','Advances.BranchID','Advances.RoutingNo')
                    ->leftJoin('Advances',function ($join) {
                        $join->on('Advances.ResStaffID','Users.StaffID')->where('Advances.AccountNo','!=',NULL);
                    })
                    ->leftJoin(DB::raw("[192.168.100.2].PIMSNew.dbo.Employer as emp"),DB::raw("emp.EmpCode"),DB::raw("Users.StaffID"))
                    ->leftJoin(DB::raw("[192.168.100.2].PIMSNew.dbo.Designation as des"),DB::raw("des.DesgCode"),DB::raw("emp.DesgCode"))
                    ->where('StaffID',$request->staffId)
                    ->where(function ($q) use ($userBusiness,$userDepartments) {
                        if (!in_array('All',$userBusiness)) {
                            $q->whereIn('Users.Business',$userBusiness);
                        }
                        if (!in_array('All',$userDepartments)) {
                            $q->orWhereIn('Users.DeptCode',$userDepartments);
                        }
                    })
                    ->orderBy('Advances.CreatedAt','desc')
                    ->first();
                if ($query) {
                    $users = $query;
                } else {
                    $users = "";
                }
                return response()->json([
                    'status' => 'success',
                    'data' => $users
                ]);
            }
        }
        return response()->json([
            'status' => 'error',
            'message' => 'Something went wrong!'
        ],404);
    }

    public function loadUsersHR_AdministrativeEntry(Request $request)
    {
        $query = User::select('StaffID as EmpCode','StaffName as Name','Designation as DesgName','Department as DeptName', 'Business', 'Email', 'Mobile','Advances.AccountNo','Advances.BankID','Advances.BranchID','Advances.RoutingNo','Branches.BranchName')
            ->leftJoin('Advances',function ($join) {
                $join->on('Advances.ResStaffID','Users.StaffID')->where('Advances.AccountNo','!=',NULL);
            })
            ->leftJoin('Branches', 'Branches.Id','Advances.BranchID')
            ->where('StaffID',$request->staffId)
            ->orderBy('Advances.CreatedAt','desc')
            ->first();
        if ($query) {
            $users = $query;
        } else {
            $users = "";
        }
        return response()->json([
            'status' => 'success',
            'data' => $users
        ]);
    }

    public function checkRepresentativeUser($staffId)
    {
        if (User::where('StaffID',$staffId)->where('RoleID','RepresentativeUser')->exists()) {
            return true;
        } else {
            return false;
        }
    }

    public function checkRequesterStaff(Request $request)
    {
        if ($request->has('staffId')) {
            $staffId = $request->staffId;
            $user = User::where('StaffID',$staffId)->first();
            return response()->json([
                'status' => 'success',
                'data' => $user
            ]);
        }
        return response()->json([
            'status' => 'error',
            'data' => null
        ]);
    }

    public function departmentUsers($subMenu)
    {

        $user = Auth::user();


        return response()->json([
            'data' => User::where('DeptCode',$user->DeptCode)
                ->where('UserId','!=',$user->UserID)
                ->where($subMenu,'Y')
                ->select('UserID','StaffName','StaffID')
                ->get()
        ]);
    }
}
