<?php

namespace Vf92\BitrixUtils\Constructor;

use Bitrix\Main;
use Vf92\BitrixUtils\Config\Version;

/**
 * Class IblockSectionUfPropEntityConstructor
 * @package Vf92\BitrixUtils\Constructor
 */
class IblockSectionUfPropEntityConstructor extends EntityConstructor
{
    const SINGLE_TYPE = 's';
    const MULTIPLE_TYPE = 'm';

    /**
     * @param int $iblockId
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     */
    public static function getDataClass($iblockId)
    {
        return static::getBaseDataClass($iblockId, static::SINGLE_TYPE);
    }

    /**
     * @param $iblockId
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     * @throws Main\ArgumentException
     */
    public static function getMultipleDataClass($iblockId)
    {
        $additionalFields = [];
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFields[] = '(new Main\ORM\Fields\Relations\Reference(
                \'USER_FIELD\',
                \Bitrix\Main\UserFieldTable::getEntity(),
                Main\Entity\Query\Join::on(\'this.FIELD_ID\', \'ref.ID\')
            ))->configureJoinType(\'inner\')';
        } else {
            $referenceFilter = '[\'=this.FIELD_ID\' => \'ref.ID\']';
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter = 'Main\Entity\Query\Join::on(\'this.FIELD_ID\', \'ref.ID\')';
            }
            $additionalFields[] = 'new Main\Entity\ReferenceField(
                \'USER_FIELD\',
                \Bitrix\Main\UserFieldTable::getEntity(),
                ' . $referenceFilter . '
            )';
        }

        return static::getBaseDataClass($iblockId, static::MULTIPLE_TYPE, $additionalFields);
    }

    /**
     * @param        $iblockId
     * @param string $type
     *
     * @param array  $additionalFields
     *
     * @return Main\Entity\DataManager|string
     * @throws Main\SystemException
     */
    protected static function getBaseDataClass($iblockId, $type = 's', array $additionalFields = [])
    {
        $className = 'Ut' . ToLower($type) . 'Iblock' . $iblockId . 'Section';
        $tableName = 'b_ut' . ToLower($type) . '_iblock_' . $iblockId . '_section';

        $additionalFieldsBase = [];
        if (Version::getInstance()->isVersionMoreEqualThan('18')) {
            $additionalFieldsBase[] = '(new Main\ORM\Fields\Relations\Reference(
                \'SECTION\',
                \Bitrix\Iblock\SectionTable::getEntity(),
                Main\Entity\Query\Join::on(\'this.VALUE_ID\', \'ref.ID\')
            ))->configureJoinType(\'inner\')';
        } else {
            $referenceFilter = '[\'=this.VALUE_ID\' => \'ref.ID\']';
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $referenceFilter = 'Main\Entity\Query\Join::on(\'this.VALUE_ID\', \'ref.ID\')';
            }
            $additionalFieldsBase[] = 'new Main\Entity\ReferenceField(
                \'SECTION\',
                \Bitrix\Iblock\SectionTable::getEntity(),
                ' . $referenceFilter . '
            )';
        }
        $additionalFields = array_merge($additionalFieldsBase, $additionalFields);
        return parent::compileEntityDataClass($className, $tableName, $additionalFields);
    }
}