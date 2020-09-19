<?php

use App\Exceptions\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

if (!function_exists('messages')) {
    function messages($key) {
        return config("API.Messages.$key");
    }
}

/**
 * Define constants function to load constant
 */
if (!function_exists('constants')) {
    function constants($key) {
        return config("API.Constant.$key");
    }
}

if (!function_exists('get_data')) {
    function getData($request)
    {
        if ($request->getContent()) {
            $input = json_decode($request->getContent(), true);
        } else {
            $input = $request->all();
        }

        //Error on empty input
        if (empty($input)) {
            return false;
        }

        return $input;
    }
}

if (!function_exists('pagination')) {
    /**
     * @param $response
     * @param null $message
     * @throws JsonResponse
     * response {data, current, total}
     */
    function pagination($response, $message = null)
    {
        if (!$message) {
            $message = config('API.message.Success');
        }

        $response['success'] = true;
        $response['message'] = $message;

        throw new JsonResponse($response, 200);
    }
}

/**
 * Global success method
 */
if (!function_exists('success')) {
    /**
     * @param $result
     * @param null $message
     * @param int $status
     */
    function success($result, $message = null, $status = 200)
    {
        if (!$message) {
            $message = config('API.message.Success');
        }

        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        response()->json($response)->send();exit;
    }
}

if (!function_exists('error')) {
    function error($error, $status = 500, $code = null)
    {
        $message = [];
        if (is_array($error)) {
            foreach ($error as $index => $errorMessage) {
                foreach ($errorMessage as $mess) {
                    $message[] = $mess;
                }
            }
        } else {
            $message[] = $error;
        }

        $response = [
            'error' => true,
            'message' => $message
        ];

        if ($code) {
            $response['status_code'] = $code;
        }

        throw new HttpResponseException(response()->json($response, $status));
    }
}

if (!function_exists('get_hash')) {
    function getHash() {
        return constants('HASH_KEY');
    }
}
