<?php


namespace Vf92\BitrixUtils\Iblock;


use Bitrix\Iblock\ElementTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Vf92\BitrixUtils\BitrixUtils;

/**
 * Class ElementHelper
 * @package Vf92\BitrixUtils\Iblock
 */
class ElementHelper
{
    /**
     * @param        $iblockId
     * @param string $code
     *
     * @return int|null
     */
    public function getIdByCode($iblockId, $code)
    {
        //SetFilter т.к. минимальная версия 16.5
        $id = 0;
        try {
            if (BitrixUtils::isVersionMoreEqualThan('16.5')) {
                $query = ElementTable::query();
                if (BitrixUtils::isVersionMoreEqualThan('17.5.2')) {
                    $query->where('CODE', $code)
                        ->where('IBLOCK_ID', $iblockId);
                } else {
                    $query->setFilter(['CODE' => $code, 'IBLOCK_ID' => $iblockId]);
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