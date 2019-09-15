<?php

namespace Vf92\BitrixUtils\Iblock;

use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Exceptions\Config\VersionException;

/**
 * Class SectionHelper
 * @package Vf92\BitrixUtils\Iblock
 */
class SectionHelper
{
    /**
     * @param int    $iblockId
     * @param string $code
     *
     * @return int|null
     * @throws VersionException
     */
    public static function getIdByCode($iblockId, $code): ?int
    {
        //SetFilter т.к. минимальная версия 16.5
        $id = 0;
        try {
            $query = SectionTable::query()->setSelect(['ID']);
            if (Version::getInstance()->isVersionLessThan('18.0.4')) {
                throw new VersionException();
            }
                $query->where('CODE', $code)->where('IBLOCK_ID', $iblockId);
            $id = (int)$query->exec()->fetch()['ID'];
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