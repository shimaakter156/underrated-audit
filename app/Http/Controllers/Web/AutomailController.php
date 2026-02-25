<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Advances;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutomailController extends Controller
{
    public function over90Days()
    {
        try {
            $closingDate = Carbon::now()->format('Y-m-d');
            $sp = "exec sp_estatement '$closingDate','',''";
            $query = DB::select($sp);
            $queryCollect = collect($query);
            $records = $queryCollect->where('AgeInDays','>=',90)->groupBy('ResStaffID');
            if (count($records)) {
                foreach ($records as $record) {
                    if (isset($record[0]->ResStaffID) && $record[0]->ResStaffID !== '') {
                        $user = Advances::where('ResStaffID', $record[0]->ResStaffID)->orderBy('CreatedAt', 'desc')->first();
                        $email = $user->ResStaffEmail;
                        $explode = explode('@', $email);
                        if ($explode[1] === 'aci-bd.com') {
                            Config::set('mail.mailers.smtp.host', 'mail.aci-bd.com');
                        } else {
                            Artisan::call('config:cache');
                        }
                        dd($email);
                        $emails[] = config('mail.mailers.smtp.host');
//                        DB::table();
                        try {
                            if ($email !== null && $email !== '' && $email !== '#N/A') {
//                                Mail::to($email)->send(new EstatementMail($data, $user->ResStaffName, $user->ResStaffID, $user->ResStaffDepartment, $user->ResStaffDesignation, Carbon::parse($request->closingDate)->format('d-m-Y')));
                            }
                        } catch (\Exception $exception) {
                            Log::error($exception->getMessage());
                            DB::table('EmailLog')->insert([
                                'Email' => $email,
                                'Message' => $exception->getMessage(),
                                'CreatedAt' => Carbon::now()
                            ]);
                        }
                    }
                }
            }
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return false;
        }
    }
}
