<?php

/**
 * api 成功 json
 */
if (!function_exists('ok')) {
    function ok($data = null)
    {
        return response()->json(['error_code' => 0, 'data' => $data]);
    }
}

/**
 * api 错误 json
 */
if (!function_exists('fail')) {
    function fail($errorMsg, $errorCode = -1)
    {
        return response()->json(['error_code' => $errorCode, 'error_message' => $errorMsg]);
    }
}
