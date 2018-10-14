<?php

namespace Vf92\BitrixUtils\Iblock;

use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Main\ArgumentException;
use InvalidArgumentException;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Iblock\Exception\IblockNotFoundException;
use Vf92\BitrixUtils\Iblock\Exception\IblockPropertyNotFoundException;

class IblockHelper
{

    /**
     * @var array
     */
    private static $iblockInfo;

    private static $propertyIdIndex = [];

    /**
     * Возвращает id инфоблока по его типу и символьному коду
     *
     * @param string $type
     * @param string $code
     *
     * @return int
     * @throws IblockNotFoundException
     * @throws ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getIblockId($type, $code)
    {
        return (int)self::getIblockField($type, $code, 'ID');
    }

    /**
     * Возвращает xml id инфоблока по его типу и символьному коду
     *
     * @param $type
     * @param $code
     *
     * @return string
     * @throws IblockNotFoundException
     * @throws ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getIblockXmlId($type, $code)
    {
        return trim(self::getIblockField($type, $code, 'XML_ID'));
    }

    /**
     * Возвращает id свойства инфоблока по символьному коду
     *
     * @param int    $iblockId
     * @param string $code
     *
     * @return int
     * @throws IblockPropertyNotFoundException
     * @throws ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getPropertyId($iblockId, $code)
    {
        $iblockId = (int)$iblockId;
        $code = trim($code);

        if ($iblockId <= 0) {
            throw new InvalidArgumentException('Iblock id must be positive integer');
        }

        if (empty($code)) {
            throw new InvalidArgumentException('Property code must be specified');
        }

        $indexKey = $iblockId . ':' . $code;

        if (isset(self::$propertyIdIndex[$indexKey])) {
            return self::$propertyIdIndex[$indexKey];
        }

        $query = PropertyTable::query()->setSelect(['ID'])->setLimit(1);
        if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
            $query->where('CODE', $code)->where('IBLOCK_ID', $iblockId);
        } else {
            $query->setFilter(['=CODE' => $code, '=IBLOCK_ID' => $iblockId]);
        }
        $property = $query->exec()->fetch();
        if ($property === false) {
            throw new IblockPropertyNotFoundException(
                sprintf(
                    'Iblock property `%s` not found in iblock #%s',
                    $code,
                    $iblockId
                )
            );
        }

        self::$propertyIdIndex[$indexKey] = (int)$property['ID'];

        return self::$propertyIdIndex[$indexKey];
    }

    /**
     * Проверка существования типа инфоблоков
     *
     * @param string $typeID
     *
     * @return bool
     * @throws ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function isIblockTypeExists($typeID)
    {
        $typeID = trim($typeID);
        if (empty($typeID)) {
            throw new InvalidArgumentException('Iblock type id must be specified');
        }

        return 1 === TypeTable::query()
                ->setSelect(['ID'])
                ->setFilter(['=ID' => $typeID])
                ->setLimit(1)
                ->exec()
                ->getSelectedRowsCount();
    }

    /**
     * @param $type
     * @param $code
     * @param $field
     *
     * @return string
     * @throws IblockNotFoundException
     * @throws ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function getIblockField($type, $code, $field)
    {
        $type = trim($type);
        $code = trim($code);

        if (empty($type) || empty($code)) {
            throw new InvalidArgumentException('Iblock type and code must be specified');
        }

        //Перед тем, как ругаться, что инфоблок не найден, попытаться перезапросить информацию из базы
        if (!isset(self::getAllIblockInfo()[$type][$code])) {
            self::$iblockInfo = null;
        }

        if (isset(self::getAllIblockInfo()[$type][$code])) {
            return trim(self::getAllIblockInfo()[$type][$code][$field]);
        }

        throw new IblockNotFoundException(
            sprintf(
                'Iblock `%s\%s` not found',
                $type,
                $code
            )
        );

    }

    /**
     * Возвращает краткую информацию обо всех инфоблоках в виде многомерного массива.
     *
     * @return array <iblock type> => <iblock code> => array of iblock fields
     * @throws ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    private static function getAllIblockInfo()
    {
        if (self::$iblockInfo === null) {
            $iblockList = IblockTable::query()
                ->setSelect(['ID', 'IBLOCK_TYPE_ID', 'CODE', 'XML_ID'])
                ->exec();
            $iblockInfo = [];
            while ($iblock = $iblockList->fetch()) {
                $iblockInfo[$iblock['IBLOCK_TYPE_ID']][$iblock['CODE']] = $iblock;
            }

            self::$iblockInfo = $iblockInfo;
        }

        return self::$iblockInfo;
    }
}
