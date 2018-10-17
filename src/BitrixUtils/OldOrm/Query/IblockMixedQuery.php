<?php

namespace Vf92\BitrixUtils\OldOrm\Query;

use CDBResult;
use CIBlockSection;

/**
 * Class IblockMixedQuery
 *
 * @package Vf92\BitrixUtils\OldOrm\Query
 */
abstract class IblockMixedQuery extends IblockSectionQuery
{
    public function doExec(): CDBResult
    {
        /** @noinspection PhpDynamicAsStaticMethodCallInspection */
        return CIBlockSection::GetMixedList(
            $this->getOrder(),
            $this->getFilterWithBase(),
            $this->isCountElements(),
            $this->getSelectWithBase()
        );
    }
}
