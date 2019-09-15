<?php

namespace Vf92\BitrixUtils\HLBlock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\DataManager;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\SystemException;
use Vf92\BitrixUtils\Exceptions\HLBlock\HLBlockException;
use Vf92\BitrixUtils\Exceptions\HLBlock\HLBlockFoundMoreThanOneException;
use Vf92\BitrixUtils\Exceptions\HLBlock\HLBlockNotFoundException;
use Vf92\BitrixUtils\Exceptions\Orm\OrmQueryException;

/**
 * Class HLBlockFactory
 * @package Vf92\BitrixUtils\HLBlock
 */
class HLBlockFactory
{
    /**
     * Возвращает скомпилированную сущность HL-блока по имени его сущности.
     *
     * @param string $hlBlockName
     *
     * @return DataManager
     * @throws ArgumentException
     * @throws HLBlockException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     */
    public static function createTableObject(string $hlBlockName): DataManager
    {
        return self::doCreateTableObject((new ConditionTree())->where('NAME', $hlBlockName));
    }

    /**
     * Возвращает скомпилированную сущность HL-блока по имени его таблицы в базе данных.
     *
     * @param string $tableName
     *
     * @return DataManager
     * @throws ArgumentException
     * @throws HLBlockException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     */
    public static function createTableObjectByTable(string $tableName): DataManager
    {
        return self::doCreateTableObject((new ConditionTree())->where('TABLE_NAME', $tableName));
    }

    /**
     * Возвращает скомпилированную сущность HL-блока по имени его таблицы в базе данных.
     *
     * @param int $id
     *
     * @return DataManager
     * @throws ArgumentException
     * @throws HLBlockException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     */
    public static function createTableObjectById(int $id): DataManager
    {
        return self::doCreateTableObject((new ConditionTree())->where('ID', $id));
    }

    /**
     * Возвращает скомпилированную сущность HL-блока по заданному фильтру, но фильтр должен в итоге находить один
     * HL-блок.
     *
     * @param ConditionTree $filter
     *
     * @return DataManager
     * @throws HLBlockException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     */
    private static function doCreateTableObject(ConditionTree $filter): DataManager
    {
        try {
            Loader::includeModule('highloadblock');
        } catch (LoaderException $e) {
            throw new HLBlockException('Ошибка компиляции сущности для HLBlock.');
        }
        try {
            $result = HighloadBlockTable::query()->where($filter)->setSelect(['*'])->exec();
            if ($result->getSelectedRowsCount() > 1) {
                throw new HLBlockFoundMoreThanOneException();
            }
            if ($result->getSelectedRowsCount() === 0) {
                throw new HLBlockNotFoundException();
            }
            $hlBlockFields = $result->fetch();
            try {
                $dataManager = HighloadBlockTable::compileEntity($hlBlockFields)->getDataClass();
                if (is_string($dataManager) || is_object($dataManager)) {
                    return new $dataManager;
                }
            } catch (SystemException $e) {
                throw new HLBlockException('Ошибка компиляции сущности для HLBlock.');
            }
        } catch (ObjectPropertyException|SystemException|ArgumentException $e) {
            throw new OrmQueryException();
        }
        throw new HLBlockException('Ошибка компиляции сущности для HLBlock.');
    }
}
