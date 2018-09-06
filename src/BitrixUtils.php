<?php

namespace Vf92;

use Bitrix\Main\Grid\Declension;
use Bitrix\Main\Result;
use DateTimeImmutable;
use DateTimeZone;

class BitrixUtils
{
    const BX_BOOL_FALSE = 'N';
    const BX_BOOL_TRUE = 'Y';

    /**
     * Определяет является ли запрос аяксовым
     *
     * @return bool
     */
    public static function isAjax()
    {
        //TODO Правильно делать не так, а смотреть на хеадеры `X-Requested-With` === `XMLHttpRequest`

        return ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest' || $_SERVER['HTTP_BX_AJAX'] == 'true');
    }

    /**
     * @param bool $value
     *
     * @return string
     */
    public static function bool2BitrixBool($value)
    {
        return $value ? self::BX_BOOL_TRUE : self::BX_BOOL_FALSE;
    }

    /**
     * @param string $value
     *
     * @return bool
     */
    public static function bitrixBool2bool($value)
    {
        return self::BX_BOOL_TRUE === $value;
    }

    /**
     * Возвращает одно сообщение об ошибке из любого Битриксового результата, где ошибки почему-то живут во
     * множественном числе.
     *
     * @param Result $result
     *
     * @return string
     */
    public static function extractErrorMessage(Result $result)
    {
        return implode('; ', $result->getErrorMessages());
    }

    /**
     * Конвертирует строку с датой и временем в формате сайта в объект DateTimeImmutable
     *
     * @param string $dateTime
     * @param string|bool $fromSite
     * @param bool $searchInSitesOnly
     * @param DateTimeZone|null $timeZone
     *
     * @return bool|DateTimeImmutable
     */
    public static function bitrixStringDateTime2DateTimeImmutable(
        $dateTime,
        $fromSite = false,
        $searchInSitesOnly = false,
        DateTimeZone $timeZone = null
    ) {
        //TODO Возможно, потребуется исправить сдвиг временной зоны
        $offsetSeconds = 0;

        return DateTimeImmutable::createFromFormat(
            DATE_ISO8601,
            sprintf(
                '%sT%s%+03d%02d',
                ConvertDateTime($dateTime, 'YYYY-MM-DD', $fromSite, $searchInSitesOnly),
                ConvertDateTime($dateTime, 'HH:MI:SS', $fromSite, $searchInSitesOnly),
                floor($offsetSeconds / 3600),
                ($offsetSeconds % 3600) / 60
            ),
            $timeZone
        );
    }

    /**
     * Конвертирует объект DateTimeImmutable в строку в формате сайта
     *
     * @param DateTimeImmutable $dateTimeImmutable
     * @param string $type
     * @param bool $site
     * @param bool $searchInSitesOnly
     *
     * @return string
     */
    public static function dateTimeImmutable2BitrixStringDate(
        DateTimeImmutable $dateTimeImmutable,
        $type = 'SHORT',
        $site = false,
        $searchInSitesOnly = false
    ) {

        //TODO Возможно, потребуется исправить сдвиг временной зоны
        return ConvertTimeStamp(
            $dateTimeImmutable->getTimestamp(),
            $type,
            $site,
            $searchInSitesOnly
        );
    }
}
