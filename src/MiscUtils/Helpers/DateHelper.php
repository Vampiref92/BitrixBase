<?php

namespace Vf92\MiscUtils\Helpers;

/**
 * Class DateHelper
 *
 * @package Vf92\MiscUtils\Helpers
 */
class DateHelper
{
    /** именительный падеж */
    public const NOMINATIVE = 'Nominative';

    /** родительный падеж */
    public const GENITIVE = 'Genitive';

    /** именительный падеж короткий*/
    public const SHORT_NOMINATIVE = 'ShortNominative';

    /** родительный падеж короткий */
    public const SHORT_GENITIVE = 'ShortGenitive';

    /** дательный падеж множ. число */
    public const DATIVE_PLURAL = 'DativePlural';

    /**Месяца в родительном падеже*/
    protected static $monthGenitive = [
        '#1#'  => 'Января',
        '#2#'  => 'Февраля',
        '#3#'  => 'Марта',
        '#4#'  => 'Апреля',
        '#5#'  => 'Мая',
        '#6#'  => 'Июня',
        '#7#'  => 'Июля',
        '#8#'  => 'Августа',
        '#9#'  => 'Сентября',
        '#10#' => 'Октября',
        '#11#' => 'Ноября',
        '#12#' => 'Декабря',
    ];

    /** Месяца в именительном падеже  */
    protected static $monthNominative = [
        '#1#'  => 'Январь',
        '#2#'  => 'Февраль',
        '#3#'  => 'Март',
        '#4#'  => 'Апрель',
        '#5#'  => 'Май',
        '#6#'  => 'Июнь',
        '#7#'  => 'Июль',
        '#8#'  => 'Август',
        '#9#'  => 'Сентябрь',
        '#10#' => 'Октябрь',
        '#11#' => 'Ноябрь',
        '#12#' => 'Декабрь',
    ];

    /** кратские месяца в именительном падеже  */
    protected static $monthShortNominative = [
        '#1#'  => 'янв',
        '#2#'  => 'фев',
        '#3#'  => 'мар',
        '#4#'  => 'апр',
        '#5#'  => 'май',
        '#6#'  => 'июн',
        '#7#'  => 'июл',
        '#8#'  => 'авг',
        '#9#'  => 'сен',
        '#10#' => 'окт',
        '#11#' => 'ноя',
        '#12#' => 'дек',
    ];

    /**кратские месяца в родительном падеже*/
    protected static $monthShortGenitive = [
        '#1#'  => 'янв',
        '#2#'  => 'фев',
        '#3#'  => 'мар',
        '#4#'  => 'апр',
        '#5#'  => 'мая',
        '#6#'  => 'июн',
        '#7#'  => 'июл',
        '#8#'  => 'авг',
        '#9#'  => 'сен',
        '#10#' => 'окт',
        '#11#' => 'ноя',
        '#12#' => 'дек',
    ];

    /**дни недели в именительном падеже*/
    protected static $dayOfWeekNominative = [
        '#1#' => 'Понедельник',
        '#2#' => 'Вторник',
        '#3#' => 'Среда',
        '#4#' => 'Четверг',
        '#5#' => 'Пятница',
        '#6#' => 'Суббота',
        '#7#' => 'Воскресенье',
    ];

    /** дни недели в множ. числе дат. падеже */
    protected static $dayOfWeekDativePlural = [
        '#1#' => 'Понедельникам',
        '#2#' => 'Вторникам',
        '#3#' => 'Средам',
        '#4#' => 'Четвергам',
        '#5#' => 'Пятницам',
        '#6#' => 'Субботам',
        '#7#' => 'Воскресеньям',
    ];

    /**краткие дни недели*/
    protected static $dayOfWeekShortNominative = [
        '#1#' => 'пн',
        '#2#' => 'вт',
        '#3#' => 'ср',
        '#4#' => 'чт',
        '#5#' => 'пт',
        '#6#' => 'сб',
        '#7#' => 'вс',
    ];

    /**
     * Подстановка русских месяцев по шаблону
     *
     * @param string $date
     *
     * @param string $case
     *
     * @param bool   $lower
     *
     * @return string
     */
    public static function replaceRuMonth(string $date, string $case = 'Nominative', bool $lower = false): string
    {
        $res = static::replaceStringByArray(
            [
                'date'    => $date,
                'case'    => $case,
                'type'    => 'month',
                'pattern' => '|#\d{1,2}#|',
            ]
        );
        if ($lower) {
            $res = ToLower($res);
        }

        return $res;
    }

    /**
     * Подстановка дней недели по шаблону
     *
     * @param string $date
     *
     * @param string $case
     *
     * @return string
     */
    public static function replaceRuDayOfWeek(string $date, string $case = 'Nominative'): string
    {
        return static::replaceStringByArray(
            [
                'date'    => $date,
                'case'    => $case,
                'type'    => 'dayOfWeek',
                'pattern' => '|#\d{1}#|',
            ]
        );
    }

    /**
     * @param array $params
     *
     * @return string
     */
    protected static function replaceStringByArray(array $params):string
    {
        preg_match($params['pattern'], $params['date'], $matches);
        if (!empty($matches[0]) && !empty($params['case'])) {
            $items = static::${$params['type'] . $params['case']};
            if (!empty($items)) {
                return str_replace($matches[0], $items[$matches[0]], $params['date']);
            }
        }

        return $params['date'];
    }
}
