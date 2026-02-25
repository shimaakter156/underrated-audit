<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EmailJob;
use App\Traits\Notification;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmailJobController extends Controller
{
    use Notification;

    public function dispatchJob()
    {
        try {
            $queues = EmailJob::where('Dispatch',0)->get();
            foreach ($queues as $queue) {
                $this->sendMail($queue->Email,$queue->ClassName,json_decode($queue->Data,true));
                EmailJob::where('JobId',$queue->JobId)->update([
                    'Dispatch' => 1
                ]);
            }
            return true;
        } catch (\Exception $exception) {
            DB::table('EmailLog')->insert([
                'Email' => 'job@error',
                'Message' => $exception->getMessage(),
                'CreatedAt' => Carbon::now()
            ]);
            return false;
        }
    }
}
