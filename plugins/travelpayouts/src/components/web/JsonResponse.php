<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\components\web;

use Travelpayouts\components\exceptions\HttpException;
use function wp_send_json;

class JsonResponse extends Response
{
    public $meta = [];

    /**
     * @inheritdoc
     */
    public function beforeSend()
    {
        parent::beforeSend();
        if (is_array($this->content)) {
            $content = $this->content;
            if (isset($content['data'], $content['meta'])) {
                $this->content = $content['data'];
                $this->meta = $content['meta'];
            }
        }

        $this->content = [
            'success' => $this->isSuccessful,
            'data' => $this->content,
            'meta' => $this->meta,
        ];
    }

    /**
     * @inheritdoc
     */
    protected function sendContent()
    {
        wp_send_json($this->content);
    }

    /**
     * @inheritdoc
     */
    public function exceptionContent()
    {
        $errorMessage = [
            'code' => $this->getStatusCode(),
            'message' => $this->statusText,
        ];

        if (TRAVELPAYOUTS_DEBUG && $exception = $this->exception) {
            $errorMessage = array_merge($errorMessage, [
                'message' => $exception instanceof HttpException
                    ? $this->statusText
                    : $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
        }

        return [
            'error' => $errorMessage,
        ];
    }
}
