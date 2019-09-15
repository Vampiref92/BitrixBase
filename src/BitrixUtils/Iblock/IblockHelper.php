<?php

namespace Vf92\BitrixUtils\Iblock;

use Bitrix\Iblock\IblockFieldTable;
use Bitrix\Iblock\IblockTable;
use Bitrix\Iblock\PropertyTable;
use Bitrix\Iblock\TypeTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Entity\ReferenceField;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use InvalidArgumentException;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Exceptions\Config\VersionException;
use Vf92\BitrixUtils\Exceptions\Iblock\IblockFieldSettingsException;
use Vf92\BitrixUtils\Exceptions\Iblock\IblockNotFoundException;
use Vf92\BitrixUtils\Exceptions\Iblock\Property\PropertyNotFoundException;

/**
 * Class IblockHelper
 * @package Vf92\BitrixUtils\Iblock
 */
class IblockHelper
{

    /**
     * @var array
     */
    private static $iblockInfo;

    /**
     * @var array
     */
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
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getIblockId($type, $code): int
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
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getIblockXmlId($type, $code): string
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
     * @throws PropertyNotFoundException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws VersionException
     */
    public static function getPropertyId($iblockId, $code): int
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
        if (Version::getInstance()->isVersionLessThan('18.0.4')) {
            throw new VersionException();
        }
        $query->where('CODE', $code)->where('IBLOCK_ID', $iblockId);
        $property = $query->exec()->fetch();
        if ($property === false) {
            throw new PropertyNotFoundException(sprintf('Iblock property `%s` not found in iblock #%s', $code,
                    $iblockId));
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
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isIblockTypeExists($typeID): bool
    {
        $typeID = trim($typeID);
        if (empty($typeID)) {
            throw new InvalidArgumentException('Iblock type id must be specified');
        }
        return 1 === TypeTable::query()->setSelect(['ID'])->setFilter(['=ID' => $typeID])->setLimit(1)->exec()->getSelectedRowsCount();
    }

    /**
     * @param $iblockId
     *
     * @return array|false
     * @throws ArgumentException
     * @throws IblockFieldSettingsException
     * @throws IblockNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getIblockCodeSettingsById($iblockId)
    {
        $iblockId = (int)$iblockId;
        if ($iblockId <= 0) {
            throw new ArgumentException('Идентификатор инфоблока не является числом, большим 0', 'iblockId');
        }
        $queryResult = IblockTable::query()->setSelect([
                'ID',
                'CODE_REQUIRED' => 'IBLOCK_FIELDS.IS_REQUIRED',
                'CODE_SETTINGS' => 'IBLOCK_FIELDS.DEFAULT_VALUE',
            ])->registerRuntimeField(new ReferenceField('IBLOCK_FIELDS', IblockFieldTable::getEntity(),
                Join::on('this.ID', 'ref.IBLOCK_ID')))->where('ID', $iblockId)->where('IBLOCK_FIELDS.FIELD_ID',
                'CODE')->exec();
        if ($item = $queryResult->fetch()) {
            $item['CODE_SETTINGS'] = unserialize($item['CODE_SETTINGS']);
            if ($item['CODE_SETTINGS'] === false) {
                throw new IblockFieldSettingsException('field settings exception');
            }
            return $item;
        }
        throw new IblockNotFoundException('инфоблок не найден');
    }

    /**
     * @param $type
     * @param $code
     * @param $field
     *
     * @return string
     * @throws IblockNotFoundException
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private static function getIblockField($type, $code, $field): string
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
        throw new IblockNotFoundException(sprintf('Iblock `%s\%s` not found', $type, $code));

    }

    /**
     * Возвращает краткую информацию обо всех инфоблоках в виде многомерного массива.
     *
     * @return array <iblock type> => <iblock code> => array of iblock fields
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private static function getAllIblockInfo(): array
    {
        if (self::$iblockInfo === null) {
            $iblockList = IblockTable::query()->setSelect(['ID', 'IBLOCK_TYPE_ID', 'CODE', 'XML_ID'])->exec();
            $iblockInfo = [];
            while ($iblock = $iblockList->fetch()) {
                $iblockInfo[$iblock['IBLOCK_TYPE_ID']][$iblock['CODE']] = $iblock;
            }
            self::$iblockInfo = $iblockInfo;
        }
        return self::$iblockInfo;
    }
}
