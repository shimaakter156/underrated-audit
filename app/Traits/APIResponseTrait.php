<?php

namespace App\Traits;


trait APIResponseTrait
{

    public function successResponseWeb($data,$message,$status){
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data'=>$data
        ],$status);
    }
    public function errorResponseWeb($message,$status){
        return response()->json([
            'status' => 'error',
            'message' => $message,
        ],$status);
    }
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