<?php

namespace app\components;

use yii\base\Component;

class JsonResponseComponent extends Component
{
    /**
     * Standard success JSON response.
     *
     * @param string $message
     * @param mixed $data
     * @return array
     */
    public function success(string $message = 'Success', $data = null): array
    {
        return [
            'success' => true,
            'message' => $message,
            'data' => $data,
        ];
    }

    /**
     * Standard error JSON response.
     *
     * @param string $message
     * @param mixed $data
     * @return array
     */
    public function error(string $message = 'Something went wrong', $data = null): array
    {
        return [
            'success' => false,
            'message' => $message,
            'data' => $data,
        ];
    }
}
