<?php

namespace Vf92\MiscUtils;

/**
 * Class EnvType
 * @package Vf92\MiscUtils
 */
class EnvType
{
    const PROD = 'prod';
    const STAGE = 'stage';
    const DEV = 'dev';

    /**
     * @return string
     */
    public static function getServerType()
    {
        if (
            (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === self::DEV)
            || (isset($_SERVER['HTTP_APP_ENV']) && $_SERVER['HTTP_APP_ENV'] === self::DEV)
            || (isset($_COOKIE['DEV']) && $_COOKIE['DEV'] === 'Y')
            || getenv('APP_ENV') === self::DEV
        ) {
            return self::DEV;
        } elseif (
            (isset($_SERVER['APP_ENV']) && $_SERVER['APP_ENV'] === self::STAGE)
            || (isset($_SERVER['HTTP_APP_ENV']) && $_SERVER['HTTP_APP_ENV'] === self::STAGE)
            || (isset($_COOKIE['STAGE']) && $_COOKIE['STAGE'] == 'Y')
            || getenv('APP_ENV') === self::STAGE
        ) {
            return self::STAGE;
        } else {
            return self::PROD;
        }
    }

    /**
     * @return bool
     */
    public static function isProd()
    {
        return self::getServerType() === self::PROD;
    }

    /**
     * @return bool
     */
    public static function isDev()
    {
        return self::getServerType() === self::DEV;
    }

    /**
     * @return bool
     */
    public static function isStage()
    {
        return self::getServerType() === self::STAGE;
    }

}
