<?php

namespace Vf92\MiscUtils\Response;

/**
 * Class JsonErrorResponse
 *
 * @package Vf92\MiscUtils\Response
 */
class JsonErrorResponse extends JsonResponse
{
    /**
     * Создаётся JsonResponse с предустановленным JsonContent и success = false
     *
     * @param array $options
     *
     * @inheritdoc
     */
    public static function create($message = null, $status = 200, $headers = [], array $options = [])
    {
        $content = static::buildContent($message, false, null, $options);
        return parent::create($content, $status, $headers);
    }

    /**
     * Создаётся JsonResponse с предустановленным JsonContent, data и success = false
     *
     * @param string $message
     * @param array  $data
     * @param int    $status
     * @param array  $options
     *
     * @return JsonResponse
     */
    public static function createWithData(
        string $message = '',
        array $data = [],
        int $status = 200,
        array $options = []
    ): JsonResponse {
        $content = static::buildContent($message, false, $data, $options);
        return parent::create($content, $status);
    }
}
