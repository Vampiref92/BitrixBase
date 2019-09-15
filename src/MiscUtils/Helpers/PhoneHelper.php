<?php

namespace Vf92\MiscUtils\Helpers;

use Vf92\MiscUtils\Exception\Helpers\Phone\WrongPhoneNumberException;
use function mb_strlen;

/**
 * Class PhoneHelper
 *
 * @package Vf92\MiscUtils\Helpers
 */
class PhoneHelper
{
    public const FORMAT_FULL = '+7 (%s%s%s) %s%s%s-%s%s-%s%s';

    public const FORMAT_DEFAULT = '+7 %s%s%s %s%s%s-%s%s-%s%s';

    public const FORMAT_URL = '8%s%s%s%s%s%s%s%s%s%s';

    public const FORMAT_INTERNATIONAL = '+7%s%s%s%s%s%s%s%s%s%s';

    public const FORMAT_SHORT = '%s%s%s%s%s%s%s%s%s%s';

    /**
     * Проверяет телефон по правилам нормализации. Допускаются только десятизначные номера с ведущими 7 или 8
     *
     * @param string $phone
     *
     * @return bool
     */
    public static function isPhone(string $phone): bool
    {
        try {
            self::normalizePhone($phone);
            return true;
        } catch (WrongPhoneNumberException $e) {
            return false;
        }
    }

    /**
     * Нормализует телефонный номер.
     * Возвращает телефонный номер в формате ********** (10 цифр без разделителя)
     * Кидает исключение, если $phone - не номер
     *
     * @param string $rawPhone
     *
     * @return string
     *
     * @throws WrongPhoneNumberException
     */
    public static function normalizePhone(string $rawPhone): string
    {
        $phone = preg_replace('~\D~', '', $rawPhone);
        if (mb_strlen($phone) > 10) {
            $phone = preg_replace('~^7|^8~', '', $phone);
        }
        if (mb_strlen($phone) === 10) {
            return $phone;
        }
        throw new WrongPhoneNumberException('Неверный номер телефона');
    }

    /**
     * Форматирует телефон по шаблону
     *
     * @param string $phone
     * @param string $format
     *
     * @return string
     */
    public static function formatPhone(string $phone, string $format = self::FORMAT_DEFAULT): string
    {
        try {
            $normalized = self::normalizePhone($phone);
            return vsprintf($format, str_split($normalized));
        } catch (WrongPhoneNumberException $e) {
            return $phone;
        }
    }
}