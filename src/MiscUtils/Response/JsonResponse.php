<?php

namespace Vf92\MiscUtils\Response;

use \Vf92\MiscUtils\Response\Model\JsonContent;
use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

/**
 * Class JsonErrorResponse
 *
 * @package Vf92\MiscUtils\Response
 */
class JsonResponse extends BaseJsonResponse
{

    /**
     * @param string $message
     * @param bool $success
     * @param array|null $data
     * @param array $options
     * @see \Vf92\MiscUtils\Response\Model\JsonContent
     *
     * @return JsonContent
     */
    public static function buildContent(string $message = '', bool $success = true, ?array $data = null, array $options = []): JsonContent
    {
        $content = new JsonContent($message, $success, $data);

        if ($options['redirect']) {
            $content->withRedirect($options['redirect']);
        }

        if (isset($options['reload'])) {
            $content->withReload((bool)$options['reload']);
        }

        return $content;
    }
}
