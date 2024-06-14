<?php
/**
 * User: 周小C
 * Date: 2024/5/24
 * Time: 13:31
 */

namespace App\Lib;

use Illuminate\Support\Facades\Response;

class ResponseHelper
{

    public static function success($data = [], $msg = '')
    {
        $respData = ['code' => 1, 'msg' => '成功', 'data' => $data];

        if ($msg) {
            $respData['msg'] = $msg;
        }
        return  $respData;

    }

    public static function error($msg = '', $data = [])
    {
        $respData = ['code' => 0, 'msg' => '失败', 'data' => $data];
        if ($msg) {
            $respData['msg'] = $msg;
        }
        return  $respData;

    }

}
