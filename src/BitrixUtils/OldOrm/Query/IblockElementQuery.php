<?php

namespace Vf92\BitrixUtils\OldOrm\Query;

use Vf92\BitrixUtils\OldOrm\Collection\CollectionBase;
use Vf92\BitrixUtils\OldOrm\Collection\IblockElementCollection;

/**
 * Class IblockElementQuery
 *
 * @package Vf92\BitrixUtils\OldOrm\Query
 */
class IblockElementQuery extends IblockQueryBase
{
    /**
     * @var int
     */
    protected $iblockId;

    public function __construct($iblockId = null)
    {
        parent::__construct();

        $this->iblockId = $iblockId;
    }

    /**
     * @return \CDBResult
     */
    public function doExec(): \CDBResult
    {
        return \CIBlockElement::GetList(
            $this->getOrder(),
            $this->getFilterWithBase(),
            $this->getGroup() ?: false,
            $this->getNav() ?: false,
            $this->getSelectWithBase()
        );
    }

    public function getBaseSelect(): array
    {
        return [
            'ACTIVE',
            'DATE_ACTIVE_FROM',
            'DATE_ACTIVE_TO',
            'IBLOCK_ID',
            'ID',
            'IBLOCK_SECTION_ID',
            'NAME',
            'XML_ID',
            'CODE',
            'SORT',
            'DETAIL_PAGE_URL',
            'SECTION_PAGE_URL',
            'LIST_PAGE_URL',
            'CANONICAL_PAGE_URL',
            'PREVIEW_TEXT',
            'DETAIL_TEXT',
        ];
    }

    /**
     * @inheritdoc
     */
    public function getBaseFilter(): array
    {
        $filter = [];

        if ($this->iblockId) {
            $filter['IBLOCK_ID'] = $this->iblockId;
        }

        return $filter;
    }

    /**
     * @inheritdoc
     */
    public function exec(): CollectionBase
    {
        return new IblockElementCollection($this->doExec());
    }
}
