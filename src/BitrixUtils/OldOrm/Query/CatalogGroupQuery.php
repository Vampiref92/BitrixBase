<?php

namespace Vf92\BitrixUtils\OldOrm\Query;

use CCatalogGroup;
use CDBResult;
use Vf92\BitrixUtils\OldOrm\Collection\CatalogGroupCollection;
use Vf92\BitrixUtils\OldOrm\Collection\CollectionBase;

class CatalogGroupQuery extends QueryBase
{
    /**
     * @inheritdoc
     */
    public function getBaseSelect()
    {
        return [
            'ID',
            'NAME',
            'BASE',
            'SORT',
            'XML_ID',
            'MODIFIED_BY',
            'CREATED_BY',
            'DATE_CREATE',
            'TIMESTAMP_X',
            'NAME_LANG',
            'CAN_ACCESS',
            'CAN_BUY',
            'CNT',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getBaseFilter()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function doExec()
    {
        return CCatalogGroup::GetList(
            $this->getOrder(),
            $this->getFilterWithBase(),
            $this->getGroup() ?: false,
            $this->getNav() ?: false,
            $this->getSelectWithBase()
        );
    }

    /**
     * @inheritdoc
     */
    public function exec()
    {
        return new CatalogGroupCollection($this->doExec());
    }

}
