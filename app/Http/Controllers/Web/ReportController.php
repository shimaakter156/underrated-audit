<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Mail\TestMail;
use App\Models\Adjustment;
use App\Models\Advances;
use App\Services\BusinessService;
use App\Services\DepartmentService;
use App\Services\EmailService;
use App\Services\SpPaginationService;
use App\Traits\Notification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use PHPMailer\PHPMailer\PHPMailer;

class ReportController extends Controller
{
    use Notification;
    //REPORT
    public function unadjustmentReport(Request $request)
    {
        $take = $request->take;
        $query = Advances::join('Users', 'Users.StaffID', 'Advances.CreatedBy')
            ->leftJoin('AdjustmentUpdate', 'AdjustmentUpdate.AdvanceID', 'Advances.AdvanceID')
            ->where(function ($q) {
                $q->where('AdjustmentUpdate.Outstanding', NULL);
                $q->orWhere('AdjustmentUpdate.Outstanding', '!=', 0)->orWhere('AdjustmentUpdate.Status', '!=', 'Approved');
            })
            ->where(function ($q) {
                $q->where('Advances.CreatedBy', Auth::user()->StaffID);
                $q->orWhere('Advances.ResStaffID', Auth::user()->StaffID);
            })
            ->where('Advances.Status', 'Approved');
        if ($request->advanceId) {
            $query->where('Advances.AdvanceID', $request->advanceId);
        }
        if ($request->requestId) {
            $query->where('Advances.RequisitionID', $request->requestId);
        }
        if ($request->resStaffId) {
            $query->where('Advances.ResStaffID', $request->resStaffId);
        }
        if ($request->business) {
            $query->where('Advances.AdvanceForBusiness', $request->business);
        }
        if ($request->department) {
            $query->where('Advances.ResStaffDepartment', $request->department);
        }
        $query->select('Advances.RequisitionID as RequisitionID', 'Advances.AdvanceID as AdvanceID',
            'Users.StaffName as RequesterStaff',
            'Advances.ResStaffID as ResponsibleStaffID',
            'Advances.ResStaffName as ResponsibleStaffName',
            DB::raw("CAST(
                CASE
                    WHEN CONVERT(INT,Advances.IsAdminEntry) = 1
                    THEN FORMAT(Advances.ApprovedAt,'dd-MM-yyyy')
                    ELSE FORMAT(Advances.CreatedAt,'dd-MM-yyyy')
                END as VARCHAR(30)
            ) as RequisitionDate"),
            DB::raw("FORMAT(Advances.AdjustmentDueDate,'dd-MM-yyyy') as AdjustmentDueDate"),
            'Advances.Purpose as PurposeOfAdvance',
            'Advances.Amount as AdvanceAmount',
            DB::raw("CAST(
                CASE
                    WHEN AdjustmentUpdate.Adjustment IS NULL
                    THEN 0
                    WHEN AdjustmentUpdate.Status = 'Pending'
                    THEN AdjustmentUpdate.PrevAdjustment
                    ELSE AdjustmentUpdate.Adjustment
                END as int
            ) as AdjustmentAmount"),
            DB::raw("CAST(
                CASE
                    WHEN AdjustmentUpdate.Outstanding IS NULL 
                    THEN Advances.Amount
                    WHEN AdjustmentUpdate.Status = 'Pending'
                    THEN AdjustmentUpdate.PrevOutstanding
                    ELSE AdjustmentUpdate.Outstanding
                END as int
            ) as OutstandingAmount"),
            DB::raw("CASE WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),GETDATE()) > 0 THEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),GETDATE()) ELSE 0 END as AgeInDays"),
            DB::raw("CAST(
                CASE 
                    WHEN DATEDIFF(day,Advances.AdjustmentDueDate,GETDATE()) >= 0
                        THEN DATEDIFF(day,Advances.AdjustmentDueDate,GETDATE())
                        ELSE 0
                    END as int
                ) as DaysOverAdjDueDate")
//            DB::raw("CAST(
//                CASE
//                    WHEN (SELECT Adjustments.Status FROM Adjustments WHERE Adjustments.Status='Pending' AND Adjustments.AdvanceID = Advances.AdvanceID) IS NULL
//                        THEN 0
//                        ELSE (SELECT Adjustments.Expense + Adjustments.Refund FROM Adjustments WHERE Status='Pending' AND Adjustments.AdvanceID = Advances.AdvanceID)
//                    END as int
//                ) as AdjustmentInProgress")
        );
        $query->groupBy('Advances.IsAdminEntry', 'AdjustmentUpdate.PrevAdjustment', 'AdjustmentUpdate.PrevOutstanding', 'AdjustmentUpdate.Status', 'Advances.RequisitionID', 'Advances.AdvanceID', 'Users.StaffName', 'Advances.ResStaffID', 'Advances.ResStaffName', 'Advances.AdjustmentDueDate', 'Advances.Purpose', 'Advances.Amount', 'AdjustmentUpdate.Adjustment', 'AdjustmentUpdate.Outstanding', 'Advances.ApprovedAt', 'Advances.CreatedAt');
        if ($request->type === 'export') {
            return response()->json([
                'data' => $query->get()
            ]);
        }
        return $query->paginate($take);
    }

    public function adjustmentReport(Request $request)
    {
        $take = $request->take;
        $query = Advances::leftJoin('Requisitions', 'Requisitions.RequisitionID', 'Advances.RequisitionID')
            ->join('Users', 'Users.StaffID', 'Advances.CreatedBy')
            ->leftJoin('AdjustmentUpdate', 'AdjustmentUpdate.AdvanceID', 'Advances.AdvanceID')
            ->leftJoin('Adjustments', 'Adjustments.AdvanceID', 'Advances.AdvanceID')
            ->where('Requisitions.CreatedBy', Auth::user()->StaffID)
            ->where('Advances.Status', 'Approved')
            ->where('Adjustments.Status', 'Approved')
            ->where('AdjustmentUpdate.Outstanding', 0);
        if ($request->advanceId) {
            $query->where('Advances.AdvanceID', $request->advanceId);
        }
        if ($request->requestId) {
            $query->where('Advances.RequisitionID', $request->requestId);
        }
        if ($request->resStaffId) {
            $query->where('Advances.ResStaffID', $request->resStaffId);
        }
        if ($request->business) {
            $query->where('Advances.AdvanceForBusiness', $request->business);
        }
        if ($request->department) {
            $query->where('Advances.ResStaffDepartment', $request->department);
        }
        $query->select('Advances.RequisitionID as RequisitionID', 'Advances.AdvanceID as AdvanceID',
            'Users.StaffName as RequesterStaff',
            'Advances.ResStaffID as ResponsibleStaffID',
            'Advances.ResStaffName as ResponsibleStaffName',
            DB::raw("FORMAT(Advances.CreatedAt,'dd-MM-yyyy') as RequisitionDate"),
            DB::raw("FORMAT(Advances.AdjustmentDueDate,'dd-MM-yyyy') as AdjustmentDueDate"),
            'Advances.Purpose as PurposeOfAdvance',
            'Advances.Amount as AdvanceAmount',
            DB::raw("SUM(Adjustments.Expense) as Expense"),
            DB::raw("
            CAST(
                CASE
                    WHEN AdjustmentUpdate.Adjustment IS NULL
                    THEN 0
                    WHEN SUM(Adjustments.Expense) >= Advances.Amount
                    THEN Advances.Amount - SUM(Adjustments.Refund)
                    ELSE
                    SUM(Adjustments.Expense)
                END
                as INT
            ) as AdjustmentAmount
            "),
            DB::raw("SUM(Adjustments.Refund) as Refund"),
            DB::raw("
            CAST(
                CASE
                    WHEN AdjustmentUpdate.Outstanding IS NULL
                    THEN 0
                    ELSE
                    AdjustmentUpdate.Outstanding
                END
                as INT
            ) as NetOutstandingAmount
            "),
            DB::raw("SUM(Adjustments.Payment) as Payment")
        );
        $query->groupBy('Advances.RequisitionID', 'Advances.AdvanceID', 'Users.StaffName', 'Advances.ResStaffID', 'Advances.ResStaffName', 'Advances.AdjustmentDueDate', 'Advances.Purpose', 'Advances.Amount', 'AdjustmentUpdate.Adjustment', 'AdjustmentUpdate.Outstanding', 'Advances.CreatedAt');
        if ($request->type === 'export') {
            return response()->json([
                'data' => $query->get()
            ]);
        }
        return $query->paginate($take);
    }

    /**
     * FINANCE AGEING REPORT
     */
    public function ageingReport(Request $request)
    {
        $take = $request->take;
        $page = $request->page;
        $offset = SpPaginationService::getOffset($page, $take);
        $query = Advances::leftJoin('Particulars', 'Particulars.ParticularId', 'Advances.ParticularId')
            ->leftJoin('Adjustments', function ($join) use ($request) {
                $join->on('Adjustments.AdvanceID', '=', 'Advances.AdvanceID')
                    ->where(function ($q) use ($request) {
                        $q->where('Adjustments.Status', 'Approved')->where('Adjustments.AdjustmentDate', '<=', $request->closingDate);
                        $q->orWhere('Adjustments.Status', NULL);
                    });
            })
            ->join('Business', 'Business.Business', 'Advances.AdvanceForBusiness')
            ->leftJoin('AdjustmentUpdate', function ($join) {
                $join->on('AdjustmentUpdate.AdvanceID', '=', 'Advances.AdvanceID')->where('AdjustmentUpdate.Outstanding', '>', 0)
                    ->where('AdjustmentUpdate.Status', 'Approved');
            })
            ->where('Advances.Status', 'Approved');
        if ($request->advanceId) {
            $query->where('Advances.AdvanceID', $request->advanceId);
        }
        if ($request->requestId) {
            $query->where('Advances.RequisitionID', $request->requestId);
        }
        if ($request->resStaffId) {
            $query->where('Advances.ResStaffID', $request->resStaffId);
        }
        if ($request->business) {
            $query->where('Advances.AdvanceForBusiness', $request->business);
        }
        if ($request->department) {
            $query->where('Advances.ResStaffDepartment', $request->department);
        }
//        $query->havingRaw('Advances.Amount - (SUM(Expense) + SUM(Refund)) > 0');
        $query->select(
            'Advances.ResStaffID as ResponsibleStaffID',
            'Advances.ResStaffName as ResponsibleStaffName',
            'Advances.ResStaffDesignation as ResponsibleStaffDesignation',
            'Advances.ResStaffDepartment as ResponsibleStaffDepartment',
            'Business.BusinessName as Business',
            'Advances.CreatedBy as RequesterStaffID',
            'Advances.RequisitionID as RequisitionID', 'Advances.AdvanceID as AdvanceID',
            DB::raw("CAST(
                CASE
                    WHEN CONVERT(INT,Advances.IsAdminEntry) = 1
                    THEN FORMAT(Advances.ApprovedAt,'dd-MM-yyyy')
                    ELSE FORMAT(Advances.CreatedAt,'dd-MM-yyyy')
                END as VARCHAR(30)
            ) as RequisitionDate"),
            DB::raw("FORMAT(Advances.AdjustmentDueDate,'dd-MM-yyyy') as AdjustmentDueDate"),
            'Advances.Amount as AdvanceAmount',
            DB::raw("CAST(
                CASE
                    WHEN SUM(Adjustments.Expense) IS NULL
                    THEN 0
                    ELSE SUM(Adjustments.Expense)
                END as int
            ) as AdjustmentAmount"),
            DB::raw("CAST(
                CASE
                    WHEN SUM(Adjustments.Refund) IS NULL
                    THEN 0
                    ELSE SUM(Adjustments.Refund)
                END as int
            ) as RefundAmount"),
            DB::raw("CAST(
                CASE
                    WHEN Adjustments.AdvanceID IS NULL
                    THEN 0
                    ELSE (SUM(Expense) + SUM(Refund))
                END as int
            ) as TotalAdjustment"),
            DB::raw("CAST(
                CASE
                    WHEN Adjustments.AdvanceID IS NULL
                    THEN Advances.Amount
                    ELSE Advances.Amount - (SUM(Expense) + SUM(Refund))
                END as int
            ) as OutstandingAmount"),
            DB::raw("FORMAT(Advances.ApprovedAt,'dd-MM-yyyy') as ApprovedDate"),
            DB::raw("CASE WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 0 THEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') ELSE 0 END as AgeInDays"),
            DB::raw("CAST(
                CASE 
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.AdjustmentDueDate),'$request->closingDate') >= 0
                        THEN DATEDIFF(day,Advances.AdjustmentDueDate,'$request->closingDate')
                        ELSE 0
                    END as int
                ) as DaysOver"),
            'Advances.Purpose as PurposeOfAdvance',
            'Particulars.Particular',
            DB::raw("CAST(
                CASE 
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') <= 30 AND Adjustments.AdvanceID IS NULL
                    THEN Advances.Amount
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') <= 30 AND Adjustments.AdvanceID IS NOT NULL
                    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
                    ELSE 0
                    END as int
                ) as '1-30'"),
            DB::raw("CAST(
                CASE 
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 30 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') <= 60 AND Adjustments.AdvanceID IS NULL
                    THEN Advances.Amount
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 30 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') <= 60 AND Adjustments.AdvanceID IS NOT NULL
                    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
                    ELSE 0
                    END as int
                ) as '31-60'"),
            DB::raw("CAST(
                CASE 
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 60 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') <= 90 AND Adjustments.AdvanceID IS NULL
                    THEN Advances.Amount
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 60 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') <= 90 AND Adjustments.AdvanceID IS NOT NULL
                    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
                    ELSE 0
                    END as int
                ) as '61-90'"),
            DB::raw("CAST(
                CASE 
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 90 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') <= 180 AND Adjustments.AdvanceID IS NULL
                    THEN Advances.Amount
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 90 AND DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') <= 180 AND Adjustments.AdvanceID IS NOT NULL
                    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
                    ELSE 0
                    END as int
                ) as '91-180'"),
            DB::raw("CAST(
                CASE 
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 180 AND Adjustments.AdvanceID IS NULL
                    THEN Advances.Amount
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 180 AND Adjustments.AdvanceID IS NOT NULL
                    THEN Advances.Amount - (SUM(Expense) + SUM(Refund))
                    ELSE 0
                    END as int
                ) as '181-Above'"),
            DB::raw("CAST(
                CASE
                    WHEN Adjustments.AdvanceID IS NULL
                    THEN Advances.Amount
                    ELSE Advances.Amount - (SUM(Expense) + SUM(Refund))
                END as int
            ) as TotalAmount")
        );
        $query->orderBy('Advances.ResStaffID', 'asc');
        $query->orderBy('RequisitionDate', 'asc');
        $query->groupBy('Adjustments.AdvanceID', 'Business.BusinessName', 'Advances.IsAdminEntry', 'Advances.ResStaffDesignation', 'Advances.ResStaffDepartment', 'AdjustmentUpdate.PrevAdjustment', 'AdjustmentUpdate.PrevOutstanding', 'Advances.RequisitionID', 'Advances.AdvanceID', 'Advances.CreatedBy', 'Advances.ResStaffID', 'Advances.ResStaffName', 'Advances.AdjustmentDueDate', 'Advances.Purpose', 'Particulars.Particular', 'Advances.Amount', 'AdjustmentUpdate.Adjustment', 'AdjustmentUpdate.Outstanding', 'AdjustmentUpdate.Status', 'Advances.CreatedAt', 'Advances.ApprovedAt');
        if ($request->type === 'export') {
            $queryData = $query->get();
            $queryData->map(function ($item) {
                if ($item->DaysOver > $item->AgeInDays) {
                    $item->DaysOver = $item->AgeInDays;
                }
            });
            $dataSet = [];
            foreach ($queryData as $each) {
                if (intval($each->OutstandingAmount) > 0) {
                    $dataSet[] = $each;
                }
            }
            return response()->json([
                'data' => $dataSet
            ]);
        }
        $countQuery = $query;
        $count = $countQuery->get();
        $data = $query->take($take)->skip($offset)->get();
        $dataSet = [];
        foreach ($data as $each) {
            if (intval($each->OutstandingAmount) > 0) {
                $dataSet[] = $each;
            }
        }
        if ($take !== '' && $offset !== '') {
            $from = $offset + 1;
            $to = ($offset + $take) <= count($count) ? $offset + $take : count($count);
        } else {
            $from = '';
            $to = '';
        }
        return response()->json([
            'data' => $dataSet,
            'from' => $from,
            'to' => $to,
            'count' => count($count)
        ]);
    }

    public function ageingReportSP(Request $request)
    {
        $closingDate = $request->closingDate;
        $take = $request->take;
        $page = $request->page;
        $offset = SpPaginationService::getOffset($page, $take);
        //PARAM CHECK START
        $param = "";
        if ($request->requestId) {
            $param .= ",'" . $request->requestId . "'";
        } else {
            $param .= ",''";
        }
        if ($request->advanceId) {
            $param .= ",'" . $request->advanceId . "'";
        } else {
            $param .= ",''";
        }
        if ($request->resStaffId) {
            $param .= ",'" . $request->resStaffId . "'";
        } else {
            $param .= ",''";
        }
        if ($request->business) {
            $param .= ",'" . $request->business . "'";
        } else {
            $param .= ",''";
        }
        if ($request->department) {
            $param .= ",'" . $request->department . "'";
        } else {
            $param .= ",''";
        }
        //PARAM CHECK END
        if ($request->type === 'export') {
            if ($param !== '') {
                $sp = "exec sp_ageing_report '$closingDate',$take,$offset,'Y'" . $param;
            } else {
                $sp = "exec sp_ageing_report '$closingDate',$take,$offset,'Y'";
            }
            return response()->json([
                'data' => SpPaginationService::getPdoResult($sp)
            ]);
        }
        $sp = "exec sp_ageing_report '$closingDate',$take,$offset,'N'" . $param;
        return SpPaginationService::paginate2($sp, $take, $offset);
    }

    public function ageingReportStaffWiseSP(Request $request)
    {
        $closingDate = $request->closingDate;
        $take = $request->take;
        $page = $request->page;
        if ($request->resStaffId === '' || $request->resStaffId === null) {
            return response()->json([
                'data' => [[], []],
                'from' => 0,
                'to' => 0
            ]);
        }
        $offset = SpPaginationService::getOffset($page, $take);
        //PARAM CHECK START
        $param = "";
        if ($request->advanceId) {
            $param .= ",'" . $request->advanceId . "'";
        } else {
            $param .= ",''";
        }
        if ($request->resStaffId) {
            $param .= ",'" . $request->resStaffId . "'";
        } else {
            $param .= ",''";
        }
        //PARAM CHECK END
        if ($request->type === 'export') {
            if ($param !== '') {
                $sp = "exec sp_ageing_report_staff_wise '$closingDate',$take,$offset,'Y'" . $param;
            } else {
                $sp = "exec sp_ageing_report_staff_wise '$closingDate',$take,$offset,'Y'" . $param;
            }
            return response()->json([
                'data' => SpPaginationService::getPdoResult($sp)
            ]);
        }
        $sp = "exec sp_ageing_report_staff_wise '$closingDate',$take,$offset,'N'" . $param;
        return SpPaginationService::paginate2($sp, $take, $offset);
    }

    public function ageingReportPOSP(Request $request)
    {
        $closingDate = $request->closingDate;
        $take = $request->take;
        $page = $request->page;
        $offset = SpPaginationService::getOffset($page, $take);
        //PARAM CHECK START
        $param = "";
        if ($request->requestId) {
            $param .= ",'" . $request->requestId . "'";
        } else {
            $param .= ",''";
        }
        if ($request->advanceId) {
            $param .= ",'" . $request->advanceId . "'";
        } else {
            $param .= ",''";
        }
        if ($request->resStaffId) {
            $param .= ",'" . $request->resStaffId . "'";
        } else {
            $param .= ",''";
        }
        if ($request->business) {
            $param .= ",'" . $request->business . "'";
        } else {
            $param .= ",''";
        }
        if ($request->department) {
            $param .= ",'" . $request->department . "'";
        } else {
            $param .= ",''";
        }
        if ($request->poNumber) {
            $param .= ",'" . $request->poNumber . "'";
        } else {
            $param .= ",''";
        }
        if ($request->supplierId) {
            $param .= ",'" . $request->supplierId . "'";
        } else {
            $param .= ",''";
        }
        //PARAM CHECK END
        if ($request->type === 'export') {
            if ($param !== '') {
                $sp = "exec sp_ageing_po_report '$closingDate',$take,$offset,'Y'" . $param;
            } else {
                $sp = "exec sp_ageing_po_report '$closingDate',$take,$offset,'Y'";
            }
            return response()->json([
                'data' => SpPaginationService::getPdoResult($sp)
            ]);
        }
        $sp = "exec sp_ageing_po_report '$closingDate',$take,$offset,'N'" . $param;
        return SpPaginationService::paginate($sp, $take, $offset);
    }

    /**
     * FINANCE REJECTED ADVANCES REPORT
     */
    public function rejectedAdvances(Request $request)
    {
        $take = $request->take;
        $query = Advances::join('Requisitions', 'Requisitions.RequisitionID', 'Advances.RequisitionID')
            ->leftJoin('PaymentModes', 'PaymentModes.PaymentMode', 'Advances.PaymentMode')
            ->leftJoin('Banks', 'Banks.BankID', 'Advances.BankID')
            ->whereIn('Advances.Status', ['Rejected']);
        $query->where(function ($q) use ($request) {
            if ($request->requestId) {
                $q->where('Requisitions.RequisitionID', $request->requestId);
            }
            if ($request->advanceId) {
                $q->where('Advances.AdvanceID', $request->advanceId);
            }
            if ($request->resStaffId) {
                $q->where('Advances.ResStaffID', $request->resStaffId);
            }
            if ($request->business) {
                $q->where('Advances.AdvanceForBusiness', $request->business);
            }
            if ($request->department) {
                $q->where('Advances.ResStaffDepartment', $request->department);
            }
            if ($request->paymentMode) {
                $q->where('Advances.PaymentMode', $request->paymentMode);
            }
            if ($request->bank) {
                $q->where('Advances.BankID', $request->bank);
            }
        });
        $query->select(DB::raw("CAST(
                CASE
                    WHEN CONVERT(INT,Advances.IsAdminEntry) = 1
                    THEN FORMAT(Advances.ApprovedAt,'dd-MM-yyyy')
                    ELSE FORMAT(Advances.CreatedAt,'dd-MM-yyyy')
                END as VARCHAR(30)
            ) as RequisitionDate"), DB::raw("FORMAT(Requisitions.PaymentRequiredBy,'dd-MM-yyyy') as PaymentRequiredBy"), 'Advances.RequisitionID', 'Advances.AdvanceID', 'Advances.CreatedBy as RequesterStaffID', 'Advances.ResStaffID as ResponsibleStaffID', 'Advances.ResStaffName as ResponsibleStaffName', 'Advances.Payee', 'Advances.Purpose', 'Advances.Amount', 'PaymentModes.PaymentModeName as PaymentMode', 'Advances.PaymentMode as PaymentModeID', 'Banks.BankName as Bank', 'Advances.BankID', 'Advances.Status', 'Advances.Reason', DB::raw("FORMAT(Advances.RejectDate,'dd-MM-yyyy') as RejectedAt"));
        return $query->paginate($take);
    }

    /**
     * FINANCE E-Statement REPORT
     */
    public function eStatement(Request $request)
    {
        $query = Advances::leftJoin('Adjustments', function ($join) {
            $join->on('Adjustments.AdvanceID', '=', 'Advances.AdvanceID')
                ->where(function ($q) {
                    $q->where('Adjustments.Status', 'Approved');
                    $q->orWhere('Adjustments.Status', NULL);
                });
        })
            ->leftJoin('AdjustmentUpdate', function ($join) {
                $join->on('AdjustmentUpdate.AdvanceID', '=', 'Advances.AdvanceID');
            })
            ->leftJoin('Vouchers', 'Vouchers.AdvanceID', 'Advances.AdvanceID')
            ->leftJoin('Users', 'Users.StaffID', 'Advances.ResStaffID')
            ->leftJoin('Users as u', 'u.StaffID', 'Advances.CreatedBy')
            ->where(function ($q) {
                $q->where('AdjustmentUpdate.Outstanding', NULL);
                $q->orWhere('AdjustmentUpdate.Outstanding', '!=', 0)->orWhere('AdjustmentUpdate.Status', '!=', 'Approved');
            })
            ->where('Advances.Status', 'Approved');
        if ($request->business) {
            $query->where('Advances.AdvanceForBusiness', $request->business);
        }
        if ($request->department) {
            $query->where('Advances.ResStaffDepartment', $request->department);
        }
        if ($request->staffId) {
            $query->where('Advances.ResStaffID', $request->staffId);
        }
        if ($request->closingDate) {
            $query->whereDate('Advances.ApprovedAt', '<=', $request->closingDate);
        }
        $query->select('Advances.RequisitionID as RequisitionID',
            'Advances.AdvanceID as AdvanceID',
            'Advances.CreatedBy as RequesterStaffID',
            'u.StaffName as RequesterStaffName',
            'ResStaffName',
            'ResStaffID',
            'ResStaffEmail',
            'ResStaffMobile',
            'ResStaffDepartment',
            'ResStaffDesignation',
            DB::raw("CAST(
                CASE
                    WHEN CONVERT(INT,Advances.IsAdminEntry) = 1
                    THEN FORMAT(Advances.ApprovedAt,'dd-MM-yyyy')
                    ELSE FORMAT(Advances.CreatedAt,'dd-MM-yyyy')
                END as VARCHAR(30)
            ) as RequisitionDate"),
            'Advances.Amount as AdvanceAmount',
            DB::raw("CAST(
                CASE
                    WHEN AdjustmentUpdate.Adjustment IS NULL
                    THEN 0
                    WHEN AdjustmentUpdate.Status = 'Pending'
                    THEN SUM(Adjustments.Expense)
                    ELSE SUM(Adjustments.Expense)
                END as int
            ) as AdjustmentAmount"),
            DB::raw("SUM(Adjustments.Refund) as RefundAmount"),
            DB::raw("CAST(
                CASE
                    WHEN AdjustmentUpdate.Outstanding IS NULL 
                    THEN Advances.Amount
                    WHEN AdjustmentUpdate.Status = 'Pending'
                    THEN AdjustmentUpdate.PrevOutstanding
                    ELSE AdjustmentUpdate.Outstanding
                END as int
            ) as OutstandingAmount"),
            DB::raw("CAST(
                CASE
                    WHEN CONVERT(INT,Advances.IsAdminEntry) = 1
                    THEN FORMAT(Advances.ApprovedAt,'dd-MM-yyyy')
                    ELSE FORMAT(Vouchers.CreatedAt,'dd-MM-yyyy')
                END as VARCHAR(30)
            ) as VoucherDate"),
            DB::raw("CASE WHEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') > 0 THEN DATEDIFF(day,DATEADD(day,5,Advances.ApprovedAt),'$request->closingDate') ELSE 0 END as AgeInDays"),
            DB::raw("CAST(
                CASE 
                    WHEN DATEDIFF(day,DATEADD(day,5,Advances.AdjustmentDueDate),GETDATE()) >= 0
                        THEN DATEDIFF(day,Advances.AdjustmentDueDate,GETDATE())
                        ELSE 0
                    END as int
                ) as DaysOver"),
            'Advances.Purpose as PurposeOfAdvance'
        );
        $query->groupBy('Advances.IsAdminEntry', 'Advances.AdjustmentDueDate', 'AdjustmentUpdate.PrevOutstanding', 'AdjustmentUpdate.PrevAdjustment', 'AdjustmentUpdate.Status', 'Advances.RequisitionID', 'Advances.AdvanceID', 'Advances.CreatedBy', 'u.StaffName', 'Advances.ResStaffID', 'Advances.ResStaffName', 'Advances.ResStaffEmail', 'Advances.ResStaffMobile', 'Advances.ResStaffDepartment', 'Advances.ResStaffDesignation', 'Advances.Purpose', 'Advances.Amount', 'AdjustmentUpdate.Adjustment', 'AdjustmentUpdate.Outstanding', 'Advances.ApprovedAt', 'Advances.CreatedAt', 'Vouchers.CreatedAt');
        $query->orderBy('Advances.RequisitionID');
//        $data = $query->get();
        $dataGrouped = $query->get()->where('OutstandingAmount', '>', 0)->groupBy('ResStaffID');
        foreach ($dataGrouped as $eachGroup) {
            $sumAdvance = $eachGroup->sum('AdvanceAmount');
            $sumOutstanding = $eachGroup->sum('OutstandingAmount');
            $sumAdjustment = $eachGroup->sum('AdjustmentAmount');
            $sumRefund = $eachGroup->sum('RefundAmount');
            $eachGroup->push([
                'RequisitionID' => '',
                'AdvanceID' => '',
                'RequesterStaffID' => '',
                'RequesterStaffName' => 'Total Amount',
                'RequisitionDate' => '',
                'AdvanceAmount' => $sumAdvance,
                'AdjustmentAmount' => $sumAdjustment,
                'RefundAmount' => $sumRefund,
                'OutstandingAmount' => $sumOutstanding,
                'VoucherDate' => '',
                'AgeInDays' => '',
                'PurposeOfAdvance' => '',
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => array_values($dataGrouped->toArray()),
            'businessList' => BusinessService::list(),
            'departmentList' => DepartmentService::departments()
        ]);
    }

    public function eStatementSP(Request $request)
    {
        //PARAM CHECK START
        $param = "";
        if ($request->business) {
            $param .= ",'" . $request->business . "'";
        } else {
            $param .= ",''";
        }
        if ($request->department) {
            $param .= ",'" . $request->department . "'";
        } else {
            $param .= ",''";
        }
        if ($request->staffId) {
            $param .= ",'" . $request->staffId . "'";
        } else {
            $param .= ",''";
        }
        //PARAM CHECK END
        $closingDate = $request->closingDate;
        $sp = "exec sp_estatement '$closingDate'" . $param;
        $query = DB::select($sp);

        $queryCollect = collect($query);
        $dataGrouped = $queryCollect->groupBy('ResStaffID');
        foreach ($dataGrouped as $eachGroup) {
            $sumAdvance = $eachGroup->sum('AdvanceAmount');
            $sumOutstanding = $eachGroup->sum('OutstandingAmount');
            $sumAdjustment = $eachGroup->sum('AdjustmentAmount');
            $sumRefund = $eachGroup->sum('RefundAmount');
            $eachGroup->push([
                'RequisitionID' => '',
                'AdvanceID' => '',
                'RequesterStaffID' => '',
                'RequesterStaffName' => 'Total Amount',
                'RequisitionDate' => '',
                'AdvanceAmount' => $sumAdvance,
                'AdjustmentAmount' => $sumAdjustment,
                'RefundAmount' => $sumRefund,
                'OutstandingAmount' => $sumOutstanding,
                'VoucherDate' => '',
                'AgeInDays' => '',
                'PurposeOfAdvance' => '',
            ]);
        }
        return response()->json([
            'status' => 'success',
            'data' => array_values($dataGrouped->toArray()),
            'businessList' => BusinessService::list(),
            'departmentList' => DepartmentService::departments()
        ]);
    }

    public function eStatementSend(Request $request)
    {
        $emails = [];
        $sms = [];
        foreach ($request->checkedData as $data) {
            if (isset($data[0]['ResStaffID']) && $data[0]['ResStaffID'] !== '') {
                $user = Advances::where('ResStaffID', $data[0]['ResStaffID'])->where('Status','Approved')->orderBy('CreatedAt', 'desc')->first();
                $email = $user->ResStaffEmail;
                $explode = explode('@', $email);
                if ($explode[1] === 'aci-bd.com') {
                    Config::set('mail.mailers.smtp.host', 'mail.aci-bd.com');
                } else {
                    Artisan::call('config:cache');
                }
                $emails[] = config('mail.mailers.smtp.host');
                $message = "Your total outstanding advance is Tk. " . $data[count($data) - 1]['OutstandingAmount'] . " as on " . Carbon::parse($request->closingDate)->format('d-m-Y') . ". Please review your statement in Advance Management System (AMS) and adjust your outstanding advance on time. Thank you. ACI Finance";
                if ($user->ResStaffMobile !== null && $user->ResStaffMobile !== '' && $user->ResStaffMobile !== '#N/A') {
                    $this->sendSmsQ($user->ResStaffMobile, $message);
                }
                $dataSet = [
                    'data' => $data,
                    'name' => $user->ResStaffName,
                    'staffId' => $user->ResStaffID,
                    'department' => $user->ResStaffDepartment,
                    'designation' => $user->ResStaffDesignation,
                    'closingDate' => Carbon::parse($request->closingDate)->format('d-m-Y')
                ];
                //SEND EMAIL
                EmailService::save($email,'\App\Mail\EstatementMail',$dataSet);
            }
        }
        return response()->json([
            'emails' => $emails
        ]);
    }

    function sendSms($receipient, $text)
    {
        try {
            $ip = '192.168.100.213';
            $userId = 'finance';
            $password = 'Asdf1234';
            $text = urlencode($text);
            $smsUrl = "http://{$ip}/httpapi/sendsms?userId={$userId}&password={$password}&smsText=" . $text . "&commaSeperatedReceiverNumbers=" . $receipient;
            $smsUrl = preg_replace("/ /", "%20", $smsUrl);
            $response = file_get_contents($smsUrl);
            return $response;
        } catch (\Exception $exception) {

        }
    }

    public function sendSmsQ($receipient, $text)
    {
        try {
            $sId = '8809617615000';
            $applicationName = 'AMS';
            $moduleName = 'E-statement';
            $otherInfo = 'N';
            $userId = Auth::user()->StaffID;
            $vendor = 'smsq';
            $message = $text;
            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'http://192.168.102.10/apps/api/send-sms/sms-master',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'To='.$receipient.'&SID='.$sId.'&ApplicationName='.urlencode($applicationName).'&ModuleName='.urlencode($moduleName).'&OtherInfo='.urlencode($otherInfo).'&userID='.$userId.'&Message='.$message.'&SmsVendor='.$vendor,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/x-www-form-urlencoded'
                ),
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false
            ));
            $response = curl_exec($curl);

            curl_close($curl);
            return $response;
        } catch (\Exception $exception) {

        }
    }

//    public function sendMail()
//    {
//        try {
//            Mail::to('shafa@aci-bd.com')->send(new EstatementMail($data, $data[0]['ResStaffName'], $data[0]['ResStaffID'], $data[0]['ResStaffDepartment'], $data[0]['ResStaffDesignation'], Carbon::parse($request->closingDate)->format('d-m-Y')));
//        } catch (\Exception $exception) {
//            dd($exception->getMessage());
//        }
//    }

    public function sendMailTest($email)
    {
        try {
            Mail::to($email)->send(new TestMail());
            return response()->json([
                'message' => 'Mail has been sent successfully.'
            ]);
        } catch (\Exception $exception) {
            DB::table('EmailLog')->insert([
                'Email' => $email,
                'Message' => $exception->getMessage(),
                'CreatedAt' => Carbon::now()
            ]);
            return response()->json([
                'message' => 'Failed to send email!'
            ]);
        }
    }

    public function sendPHPMailerEmail($email)
    {
        require base_path("vendor/autoload.php");
        $mail = new PHPMailer(true);
        try {
            // Email server settings
            $mail->SMTPDebug = 1;
            $mail->isSMTP();
            $mail->isHTML();
            $mail->Host = 'mail.pingmaster.net';             //  smtp host
            $mail->Port = 587;
            $mail->SMTPAuth = true;
            $mail->Username = 'do-not-reply@pingmaster.net';
            $mail->Password = 'mthQSUTH,u%I';
            $mail->setFrom('do-not-reply@pingmaster.net', 'no-reply');
            $mail->addAddress($email);
            $mail->Subject = 'AMS TEST MAIL';
            $mail->Body = "TEST AMS";
            // $mail->AltBody = plain text version of email body;
            if (!$mail->send()) {
                return $mail->ErrorInfo;
            } else {
                return 'success';
            }
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

//    public function businessWiseAdvance(Request $request)
//    {
//        try {
//            $take = $request->take;
//            $query = Advances::leftJoin('Adjustments', 'Adjustments.AdvanceID', 'Advances.AdvanceID')
//                ->leftJoin('AdjustmentUpdate', 'AdjustmentUpdate.AdvanceID', 'Advances.AdvanceID')
//                ->join('Business', 'Business.Business', 'Advances.AdvanceForBusiness')
//                ->where(function ($q) {
//                    $q->where('Adjustments.Status', 'Approved');
//                    $q->orWhere('Adjustments.Status', NULL);
//                })
//                ->where('Advances.Status', 'Approved')
//                ->where(function ($q) {
//                    $q->where('AdjustmentUpdate.Outstanding', '>', 0);
//                    $q->orWhere('AdjustmentUpdate.Outstanding', NULL);
//                })
//                ->where(function ($q) {
//                    $q->where('AdjustmentUpdate.Status', 'Approved');
//                    $q->orWhere('AdjustmentUpdate.Status', NULL);
//                });
//            if ($request->advanceId) {
//                $query->where('Advances.AdvanceID', $request->advanceId);
//            }
//            if ($request->requestId) {
//                $query->where('Advances.RequisitionID', $request->requestId);
//            }
//            if ($request->resStaffId) {
//                $query->where('Advances.ResStaffID', $request->resStaffId);
//            }
//            if ($request->business) {
//                $query->where('Advances.AdvanceForBusiness', $request->business);
//            }
//            if ($request->department) {
//                $query->where('Advances.ResStaffDepartment', $request->department);
//            }
//            $query->select(
//                'Business.BusinessName',
//                DB::raw('SUM(DISTINCT Advances.Amount) as AdvanceAmount'),
//                DB::raw("CAST(
//                CASE
//                    WHEN SUM(Adjustments.Expense) IS NULL
//                    THEN 0
//                    ELSE SUM(Adjustments.Expense)
//                END as int
//            ) as ExpenseAmount"),
//                DB::raw("CAST(
//                CASE
//                    WHEN SUM(Adjustments.Expense) IS NULL
//                    THEN 0
//                    ELSE SUM(Adjustments.Expense)
//                END as int
//            ) as AdjustmentAmount"),
//                DB::raw("CAST(
//                CASE
//                    WHEN SUM(Adjustments.Refund) IS NULL
//                    THEN 0
//                    ELSE SUM(Adjustments.Refund)
//                END as int
//            ) as RefundAmount"),
//                DB::raw("CAST(
//                CASE
//                    WHEN SUM(Adjustments.Payment) IS NULL
//                    THEN 0
//                    ELSE SUM(Adjustments.Payment)
//                END as int
//            ) as PaymentAmount"),
//                DB::raw("CAST(
//                CASE
//                    WHEN SUM(AdjustmentUpdate.Outstanding) IS NULL
//                    THEN SUM(DISTINCT Advances.Amount)
//                    ELSE SUM(DISTINCT Advances.Amount) - (SUM(Adjustments.Expense) + SUM(Adjustments.Refund) - SUM(Adjustments.Payment))
//                END as int
//            ) as OutstandingAmount")
//            );
//            $query->orderBy('Business.BusinessName');
//            $query->groupBy('Business.BusinessName', 'Advances.AdvanceForBusiness');
//            return $query->paginate($take);
//        } catch (\Exception $exception) {
//            return $exception->getMessage();
//        }
//    }

    public function businessWiseAdvance(Request $request)
    {
        try {
            $business = $request->business;
            return response()->json([
                'status' => 'success',
                'data' => DB::select("EXEC sp_business_wise_report '$business'")
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong!'
            ], 500);
        }
    }
}
