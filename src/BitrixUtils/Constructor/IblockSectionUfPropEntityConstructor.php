<?php

namespace Vf92\BitrixUtils\Constructor;

use Bitrix\Main;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Exceptions\Config\VersionException;

/**
 * Class IblockSectionUfPropEntityConstructor
 * @package Vf92\BitrixUtils\Constructor
 */
class IblockSectionUfPropEntityConstructor extends EntityConstructor
{
    public const SINGLE_TYPE = 's';
    public const MULTIPLE_TYPE = 'm';

    /**
     * @param int $iblockId
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     * @throws VersionException
     */
    public static function getDataClass(int $iblockId)
    {
        return static::getBaseDataClass($iblockId, static::SINGLE_TYPE);
    }

    /**
     * @param int $iblockId
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     * @throws VersionException
     */
    public static function getMultipleDataClass(int $iblockId)
    {
        $additionalFields = [];
        if (Version::getInstance()->isVersionLessThan('18.0.4')) {
            throw new VersionException();
        }
        $additionalFields[] = '(new Main\ORM\Fields\Relations\Reference(
                \'USER_FIELD\',
                \Bitrix\Main\UserFieldTable::getEntity(),
                Main\Entity\Query\Join::on(\'this.FIELD_ID\', \'ref.ID\')
            ))->configureJoinType(\'inner\')';
        return static::getBaseDataClass($iblockId, static::MULTIPLE_TYPE, $additionalFields);
    }

    /**
     * @param int    $iblockId
     * @param string $type
     *
     * @param array  $additionalFields
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     * @throws VersionException
     */
    protected static function getBaseDataClass(int $iblockId, string $type = 's', array $additionalFields = [])
    {
        $className = 'Ut' . ToLower($type) . 'Iblock' . $iblockId . 'Section';
        $tableName = 'b_ut' . ToLower($type) . '_iblock_' . $iblockId . '_section';
        $additionalFieldsBase = [];
        if (Version::getInstance()->isVersionLessThan('18.0.4')) {
            throw new VersionException();
        }
        $additionalFieldsBase[] = '(new Main\ORM\Fields\Relations\Reference(
                \'SECTION\',
                \Bitrix\Iblock\SectionTable::getEntity(),
                Main\Entity\Query\Join::on(\'this.VALUE_ID\', \'ref.ID\')
            ))->configureJoinType(\'inner\')';
        $additionalFields = array_merge($additionalFieldsBase, $additionalFields);
        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }
}