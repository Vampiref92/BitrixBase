<?php

namespace Vf92\BitrixUtils\Constructor;

use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\SystemException;

class IblockPropMultipleEntityConstructor extends EntityConstructor
{
    /**
     * @param int $iblockId
     *
     * @return DataManager|string
     * @throws SystemException
     * @deprecated
     */
    public static function getDataClass($iblockId)
    {
        return IblockPropEntityConstructor::getMultipleDataClass($iblockId);
    }
}