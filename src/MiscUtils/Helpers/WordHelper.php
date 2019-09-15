<?php

namespace Vf92\MiscUtils\Helpers;

/**
 * Class WordHelper
 * @package Vf92\MiscUtils\Helpers
 */
class WordHelper
{
    /**
     * Возвращает нужную форму существительного, стоящего после числительного
     *
     * @param int   $number числительное
     * @param array $forms  формы слова для 1, 2, 5. Напр. ['дверь', 'двери', 'дверей']
     *
     * @return string
     */
    public static function declension(int $number, array $forms): string
    {
        $ar = [2, 0, 1, 1, 1, 2];
        $key = ($number % 100 > 4 && $number % 100 < 20) ? 2 : $ar[min($number % 10, 5)];
        return $forms[$key];
    }

    /**
     * Возвращает отформатированный вес
     *
     * @param float $weight
     * @param bool  $short
     *
     * @param int   $fullLimit
     *
     * @return string
     */
    public static function showWeight(float $weight, bool $short = false, int $fullLimit = 0): string
    {
        if ($short && ($fullLimit === 0 || ($fullLimit > 0 && $weight > $fullLimit))) {
            return static::numberFormat($weight / 1000, 2, true) . ' кг';
        }
        $parts = [];
        $kg = floor($weight / 1000);
        if ($kg) {
            $parts[] = static::numberFormat($kg, 0) . ' кг';
        }
        $g = $weight % 1000;
        if ($g) {
            $parts[] = $g . ' г';
        }
        return implode(' ', $parts);
    }

    /**
     * Возвращает отформатированную длину в см - задается в мм
     *
     * @param float $lengthMm - длинна в миллиметрах
     *
     * @return string
     */
    public static function showLengthByMillimeters(float $lengthMm): string
    {
        return static::numberFormat($lengthMm / 10, 1, true) . ' см';
    }

    /**
     * Форматированный вывод чиел, с возможностью удаления незначащих нулей и с округлением до нужной точности
     *
     * @param  float|int    $number
     * @param int  $decimals
     *
     * @param bool $delEndNull
     *
     * @return string
     */
    public static function numberFormat($number, int $decimals = 2, bool $delEndNull = false): string
    {
        $res = number_format($number, $decimals, '.', ' ');
        if ($delEndNull) {
            $res = rtrim($res, '0');
            $res = rtrim($res, '.');
        }
        return $res;
    }

    /**
     * Очистка текста от примесей(тегов, лишних спец. символов)
     *
     * @param string $string
     *
     * @return mixed
     */
    public static function clear(string $string)
    {
        return str_replace(["\r", PHP_EOL], '', strip_tags(html_entity_decode($string)));
    }

    /**
     * @param float|int $size
     *
     * @return string
     */
    public static function formatSize($size): string
    {
        $fileSizeName = [' Bytes', ' KB', ' MB', ' GB', ' TB', ' PB', ' EB', ' ZB', ' YB'];
        $i = (int)floor(log($size, 1024));
        return $size ? round($size / (1024 ** $i), 2) . $fileSizeName[$i] : '0 ' . $fileSizeName[0];
    }
}
