<?php
/**
 * Created by PhpStorm.
 * User: frolov
 * Date: 12.09.18
 * Time: 18:07
 */

namespace Vf92\Iblock;


use Bitrix\Iblock\SectionTable;
use Bitrix\Main\Entity\DataManager;

class SectionOrm extends DataManager
{
    public function exec()
    {
        $query = SectionTable::query();
        $query->registerRuntimeField()
        return $query->exec();
    }
}