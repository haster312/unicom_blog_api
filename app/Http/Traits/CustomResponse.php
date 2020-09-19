<?php

namespace App\Http\Traits;

use App\Exceptions\JsonResponse;
use Illuminate\Http\Exceptions\HttpResponseException;

trait CustomResponse
{
    /**
     * Return pagination data with total
     * @param $data
     * @param $message
     * @throws JsonResponse
     */
    public function pagination($data, $message = null)
    {
        if (!$message) {
            $message = config('API.message.Success');
        }

        $response = [
            'success' => true,
            'data' => $data['result'],
            'current' => $data['current'],
            'total' => $data['total'],
            'message' => $message
        ];

        throw new JsonResponse($response, 200);
    }

    /**
     * Send success response
     * @param $result
     * @param $message
     * @param $status
     * @throws JsonResponse
     */
    public function success($result, $message = null, $status = 200)
    {
        if (!$message) {
            $message = config('API.message.Success');
        }

        $response = [
            'success' => true,
            'data' => $result,
            'message' => $message,
        ];

        throw new JsonResponse($response, $status);
    }

    /**
     * Send error response
     * @param $error
     * @param int $status
     * @param null $code
     */
    public function error($error, $status = 500, $code = null)
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
