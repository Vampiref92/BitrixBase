<?php


namespace Vf92\BitrixUtils\Iblock;


use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Vf92\BitrixUtils\BitrixUtils;

/**
 * Class SectionHelper
 * @package Vf92\BitrixUtils\Iblock
 */
class SectionHelper
{
    /**
     * @param string $code
     *
     * @return int|null
     */
    public function getIdByCode($code)
    {
        //SetFilter т.к. минимальная версия 16.5
        $id = 0;
        try {
            if (BitrixUtils::isVersionMoreEqualThan('16.5')) {
                $query = SectionTable::query();
                if (BitrixUtils::isVersionMoreEqualThan('17.5.2')) {
                    $query->where('CODE', $code);
                } else {
                    $query->setFilter(['CODE' => $code]);
                }
                $id = (int)$query->exec()->fetch()['ID'];
            }
        } catch (ObjectPropertyException $e) {
            return null;
        } catch (ArgumentException $e) {
            return null;
        } catch (SystemException $e) {
            return null;
        }
        return $id > 0 ? $id : null;
    }
}