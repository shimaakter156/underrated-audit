<?php

namespace App\Traits;


trait APIResponseTrait
{
    public  function successResponse($data,$message){
        return response()->json([
            'status' => 1,
            'message' => $message,
            'data'=>$data
        ],200);
    }

    public  function errorResponse($message){
        return response()->json([
            'status' => 0,
            'message' => $message,
        ],404);
    }
}