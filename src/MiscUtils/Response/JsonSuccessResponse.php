<?php

namespace Vf92\MiscUtils\Response;

/**
 * Class JsonSuccessResponse
 *
 * @package Vf92\MiscUtils\Response
 */
class JsonSuccessResponse extends JsonResponse
{
    /**
     * Создаётся JsonResponse с предустановленным JsonContent и success = true
     *
     * @inheritdoc
     */
    public static function create($message = null, $status = 200, $headers = [], array $options = [])
    {
        $content = static::buildContent($message, true, null, $options);

        return parent::create($content, $status, $headers);
    }

    /**
     * Создаётся JsonResponse с предустановленным JsonContent, data и success = true
     *
     * @param string $message
     * @param array  $data
     * @param int    $status
     *
     * @param array  $options
     *
     * @return JsonResponse
     */
    public static function createWithData(
        $message = '',
        array $data = [],
        $status = 200,
        array $options = []
    ) {
        $content = static::buildContent($message, true, $data, $options);

        return parent::create($content, $status);
    }
}
