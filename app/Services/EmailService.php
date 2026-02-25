<?php

namespace App\Services;

use App\Models\EmailJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class EmailService
{
    public static function save($email,$class,$data)
    {
        try {
            EmailJob::create([
                'Email' => $email,
                'ClassName' => $class,
                'Data' => json_encode($data),
                'CreatedAt' => Carbon::now()
            ]);
        } catch (\Exception $exception) {
            DB::table('EmailLog')->insert([
                'Email' => $email,
                'Message' => $exception->getMessage(),
                'CreatedAt' => Carbon::now()
            ]);
        }
    }
}