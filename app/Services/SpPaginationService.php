<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class SpPaginationService
{
    public static function paginate($sp,$take='',$offset='')
    {
        $dataSet = SpPaginationService::getPdoResult($sp);
        if ($take !== '' && $offset !== '') {
            $from = $offset + 1;
            $to = ($offset + $take) <= intval($dataSet[1][0]['CountData']) ? $offset + $take : intval($dataSet[1][0]['CountData']);
        } else {
            $from = '';
            $to = '';
        }
        return response()->json([
            'data' => $dataSet,
            'from' => $from,
            'to' => $to
        ]);
    }

    public static function paginate2($sp,$take='',$offset='')
    {
        $dataSet = SpPaginationService::getPdoResult($sp);
        $countData = count($dataSet[0]) > 0 ? $dataSet[0][0]['CountData']:0;
        $dataSet[] = count($dataSet[0]) > 0 ? [['CountData' => intval($countData)]]:[['CountData' => 0]];
        if ($take !== '' && $offset !== '') {
            $from = $offset + 1;
            $to = ($offset + $take) <= intval($countData) ? $offset + $take : intval($countData);
        } else {
            $from = '';
            $to = '';
        }
        return response()->json([
            'data' => $dataSet,
            'from' => $from,
            'to' => $to
        ]);
    }

    public static function getOffset($page,$take)
    {
        return $page === 1 ? 0 : $take * ($page - 1);
    }

    public static function getPdoResult($sql)
    {
        $conn = DB::connection('sqlsrv');
        $pdo = $conn->getPdo()->prepare($sql);
        $pdo->execute();
        $res = array();
        do {
            $rows = $pdo->fetchAll(\PDO::FETCH_ASSOC);
            $res[] = $rows;
        } while ($pdo->nextRowset());
        return $res;
    }
}