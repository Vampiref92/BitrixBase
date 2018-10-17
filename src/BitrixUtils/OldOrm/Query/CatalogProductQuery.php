<?php

namespace Vf92\BitrixUtils\OldOrm\Query;

use CDBResult;
use Vf92\BitrixUtils\OldOrm\Collection\CatalogProductCollection;
use Vf92\BitrixUtils\OldOrm\Collection\CollectionBase;

class CatalogProductQuery extends QueryBase
{

    /**
     * Исполняет запрос и возвращает коллекцию сущностей. Например, элементов инфоблока.
     *
     * @return CollectionBase
     */
    public function exec(): CollectionBase
    {
        return new CatalogProductCollection($this->doExec());
    }

    /**
     * Непосредственное выполнение запроса через API Битрикса
     *
     * @return CDBResult
     */
    public function doExec(): CDBResult
    {
        return \CCatalogProduct::GetList(
            $this->getOrder(),
            $this->getFilterWithBase(),
            $this->getGroup() ?: false,
            $this->getNav() ?: false,
            $this->getSelectWithBase()
        );
    }

    /**
     * Возвращает базовый фильтр - та его часть, которую нельзя изменить. Например, ID инфоблока.
     *
     * @return array
     */
    public function getBaseFilter(): array
    {
        return [];
    }

    /**
     * Возвращает базовую выборку полей. Например, те поля, которые обязательно нужны для создания сущности.
     *
     * @return array
     */
    public function getBaseSelect(): array
    {
        return [
            'ID',
            'QUANTITY',
            'WEIGHT',
            'WIDTH',
            'LENGTH',
            'HEIGHT',
            'MEASURE',
            'ELEMENT_IBLOCK_ID',
            'ELEMENT_XML_ID',
            'ELEMENT_NAME',
        ];
    }
}
