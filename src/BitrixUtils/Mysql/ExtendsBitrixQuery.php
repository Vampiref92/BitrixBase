<?php
namespace Vf92\BitrixUtils\Mysql;

use Bitrix\Main\Entity\Query;

/**
 * Class ExtendsBitrixQuery
 * @package Vf92\BitrixUtils\Mysql
 */
class ExtendsBitrixQuery extends Query
{
    /**
     * заполняем объект
     * @noinspection MagicMethodsValidityInspection
     * @noinspection PhpMissingParentConstructorInspection
     * @inheritdoc */
    public function __construct($source)
    {
        /** reInit Query*/
        if ($source instanceof Query) {
            $this->entity = clone $source->getEntity();

            /** сброс алиасов */
            $this->setCustomBaseTableAlias($this->entity->getDBTableName());

            /** set old array filter*/
            $this->setFilter($source->getFilter());

            /** set order and limit */
            $this->setOrder($source->getOrder());
            $this->setLimit($source->getLimit());

            /** set filter by query - new filter where */
            $this->filterHandler = $source->filterHandler;
            $this->whereHandler = $source->whereHandler;
            $this->havingHandler = $source->havingHandler;
        }
        $this->buildQuery();
    }

    /**
     * Получаем сформированное условие по запросу(where)
     * @return string
     */
    public function getBuildWhere(): string
    {
        $sql = $this->query_build_parts['WHERE'];

        return !empty($sql) ? ' WHERE ' . $sql : '';
    }

    /**
     * Получаем сформированную сортировку(order)
     * @return string
     */
    public function getBuildOrder(): string
    {
        $sql = $this->query_build_parts['ORDER'];

        return !empty($sql) ? ' ORDER BY ' . $sql : '';
    }
}