<?php

namespace Vf92\BitrixUtils\Helpers;

use Bitrix\Main\ObjectException;
use Bitrix\Main\Type\Date;
use Bitrix\Main\Type\DateTime;
use DateTime as NormalDateTime;
use Exception;
use Vf92\MiscUtils\Helpers\DateHelper as MiscDateHelper;

/**
 * Class DateHelper
 *
 * @package Vf92\BitrixUtils\Helpers
 */
class DateHelper extends MiscDateHelper
{
    /**
     * Преобразование битриксового объекта даты в Php
     *
     * @param DateTime $bxDatetime
     *
     * @return NormalDateTime
     * @throws Exception
     */
    public static function convertToDateTime(DateTime $bxDatetime): NormalDateTime
    {
        return (new NormalDateTime())->setTimestamp($bxDatetime->getTimestamp());
    }

    /**
     * Враппер для FormatDate. Доп. возможности
     *  - ll - отображение для недели в винительном падеже (в пятницу, в субботу)
     *  - XX - 'Сегодня', 'Завтра'
     *
     * @param string $dateFormat
     * @param int    $timestamp
     *
     * @return string
     * @throws Exception
     */
    public static function formatDate(string $dateFormat, int $timestamp): string
    {
        $date = (new NormalDateTime)->setTimestamp($timestamp);
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
            $currentDate = new NormalDateTime();
            $tmpDate->setTime(0, 0, 0);
            $currentDate->setTime(0, 0, 0);
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

    /**
     * @param string $dateFrom
     * @param string $dateTo
     * @param array  $setting
     *
     * @return string
     * @throws ObjectException
     */
    public static function getFormattedActiveDate(string $dateFrom = '', string $dateTo = '', array $setting = []): string
    {
        $result = '';
        if (!isset($setting['with_text'])) {
            $setting['with_text'] = 'с';
        }
        if (!isset($setting['to_text'])) {
            $setting['to_text'] = 'по';
        }
        if (!isset($setting['to_text2'])) {
            $setting['to_text2'] = 'до';
        }
        if (!isset($setting['year_text'])) {
            $setting['year_text'] = 'года';
        }
        $currentDate = new Date();
        if (!empty($dateFrom) && !empty($dateTo)) {
            $result = $setting['with_text'] . ' ';
            $dateFromGen = new Date($dateFrom);
            $dateToGen = new Date($dateTo);
            if ((int)$dateFromGen->format('Y') === (int)$dateToGen->format('Y')) {
                if ((int)$dateFromGen->format('n') === $dateToGen->format('n')) {
                    $result .= $dateFromGen->format('d');
                    $result .= ' ' . $setting['to_text'] . ' ';
                    $result .= static::replaceRuMonth($dateFromGen->format('d #n#'), static::GENITIVE);
                } else {
                    $result .= static::replaceRuMonth($dateFromGen->format('d #n#'), static::GENITIVE);
                    $result .= ' ' . $setting['to_text'] . ' ';
                    $result .= static::replaceRuMonth($dateToGen->format('d #n#'), static::GENITIVE);
                }
                if ((int)$dateFromGen->format('Y') !== $dateFromGen->format('Y')) {
                    $result .= $dateFromGen->format('Y года');
                }
            } else {
                $result .= static::replaceRuMonth($dateFromGen->format('d #n# Y года'), static::GENITIVE);
                $result .= ' ' . $setting['to_text'] . ' ';
                $result .= static::replaceRuMonth($dateToGen->format('d #n# Y года'), static::GENITIVE);
            }
        } elseif (!empty($dateFrom)) {
            $result = $setting['with_text'] . ' ';
            $dateFromGen = new Date($dateFrom);
            if ((int)$dateFromGen->format('Y') === $currentDate->format('Y')) {
                $result .= static::replaceRuMonth($dateFromGen->format('d #n#'), static::GENITIVE);
            } else {
                $result .= static::replaceRuMonth($dateFromGen->format('d #n# Y года'), static::GENITIVE);
            }
        } elseif (!empty($dateTo)) {
            $result = $setting['to_text2'] . ' ';
            $dateToGen = new Date($dateTo);
            if ((int)$dateToGen->format('Y') === $currentDate->format('Y')) {
                $result .= static::replaceRuMonth($dateToGen->format('d #n#'), static::GENITIVE);
            } else {
                $result .= static::replaceRuMonth($dateToGen->format('d #n# Y ' . $setting['year_text']),
                    static::GENITIVE);
            }
        }
        return $result;
    }
}
