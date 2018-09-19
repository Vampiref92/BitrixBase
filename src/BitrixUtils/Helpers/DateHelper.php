<?php
namespace Vf92\BitrixUtils\Helpers;

use Bitrix\Main\Type\DateTime;
use DateTime as NormalDateTime;
use Vf92\BitrixUtils\MiscUtils\Helpers\DateHelper as MiscDateHelper;

/**
 * Class DateHelper
 *
 * @package Vf92\BitrixUtils\Helpers
 */
class DateHelper extends MiscDateHelper
{
    /**
     * Преобразование битриксового объекта даты в Php
     * @param DateTime $bxDatetime
     *
     * @return NormalDateTime
     */
    public static function convertToDateTime(DateTime $bxDatetime)
    {
        return (new NormalDateTime())->setTimestamp($bxDatetime->getTimestamp());
    }

    /**
     * Враппер для FormatDate. Доп. возможности
     *  - ll - отображение для недели в винительном падеже (в пятницу, в субботу)
     *  - XX - 'Сегодня', 'Завтра'
     * @param string $dateFormat
     * @param int $timestamp
     *
     * @return string
     */
    public static function formatDate($dateFormat, $timestamp)
    {
        $date = (new \DateTime)->setTimestamp($timestamp);
        if (false !== mb_strpos($dateFormat, 'll')) {
            $str = null;
            switch ($date->format('w')) {
                case 0:
                    $str = 'в воскресенье';
                    break;
                case 1:
                    $str = 'в понедельник';
                    break;
                case 2:
                    $str = 'во вторник';
                    break;
                case 3:
                    $str = 'в среду';
                    break;
                case 4:
                    $str = 'в четверг';
                    break;
                case 5:
                    $str = 'в пятницу';
                    break;
                case 6:
                    $str = 'в субботу';
                    break;
            }
            if (null !== $str) {
                $dateFormat = str_replace('ll', $str, $dateFormat);
            }
        }
        if (false !== mb_strpos($dateFormat, 'XX')) {
            $tmpDate = clone $date;
            $currentDate = new \DateTime();
            $tmpDate->setTime(0,0,0,0);
            $currentDate->setTime(0,0,0,0);

            $diff = $tmpDate->diff($currentDate)->days;
            switch (true) {
                case $diff === 0:
                    $str = 'Сегодня';
                    break;
                case $diff === 1:
                    $str = 'Завтра';
                    break;
                default:
                    $str = 'j F';
            }
            $dateFormat = str_replace('XX', $str, $dateFormat);
        }

        return FormatDate($dateFormat, $timestamp);
    }
}
