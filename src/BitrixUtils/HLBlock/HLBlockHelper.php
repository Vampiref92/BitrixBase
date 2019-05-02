<?php

namespace Vf92\BitrixUtils\HLBlock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\DB\Result;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Fields\Relations\Reference;
use Bitrix\Main\ORM\Query\Join;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserFieldTable;
use Vf92\BitrixUtils\Constructor\EntityConstructor;

/**
 * Class HLBlockHelper
 *
 * @package Vf92\BitrixUtils\HLBlock
 */
class HLBlockHelper
{
    /**
     * Получение ID Хайлоад блока по имени
     *
     * @param string $name
     *
     * @throws ArgumentException
     * @throws LoaderException
     * @throws SystemException
     * @return int
     */
    public static function getIdByName($name)
    {
        $params = [
            'select' => ['ID'],
            'filter' => ['NAME' => $name],
            'cache'  => ['ttl' => 360000],
        ];

        return (int)static::getHighloadTableRes($params)->fetch()['ID'];
    }

    /**
     * @param array $params
     *
     * @return Result
     * @throws ArgumentException
     * @throws LoaderException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getHighloadTableRes(array $params)
    {
        Loader::includeModule('highloadblock');

        return HighloadBlockTable::getList($params);
    }

    /**
     * Получение ID Хайлоад блока по таблице
     *
     * @param string $name
     *
     * @throws ArgumentException
     * @throws LoaderException
     * @throws SystemException
     * @return int
     */
    public static function getIdByTableName($name)
    {
        $params = [
            'select' => ['ID'],
            'filter' => ['TABLE_NAME' => $name],
            'cache'  => ['ttl' => 360000],
        ];

        return (int)static::getHighloadTableRes($params)->fetch()['ID'];
    }

    /**
     * @param array $select
     *
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getHLList(array $select = ['ID', 'NAME'])
    {
        $HLList = [];

        if (!is_array($select) || empty($select)) {
            $select = ['ID', 'NAME'];
        }

        $res = HighloadBlockTable::query()->setSelect($select)->exec();
        while ($hlItem = $res->fetch()) {
            $HLList[] = $hlItem;
        }

        return $HLList;
    }

    /**
     * @param int    $id
     *
     * @param string $lang
     *
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getHLFields($id, $lang = 'ru')
    {
        $fields = [];

        $dataManager = EntityConstructor::compileEntityDataClass('UserFieldLang', 'b_user_field_lang',
            ['new Main\ORM\Fields\Relations\Reference(\'USER_FIELD\', Main\UserFieldTable::getEntity(), Main\Entity\Query\Join::on(\'this.USER_FIELD_ID\', \'ref.ID\'))']);

        $query = $dataManager::query();
        $res = $query->setSelect([
            'EDIT_FORM_LABEL',
            'LIST_COLUMN_LABEL',
            'ENTITY_ID'     => 'USER_FIELD.ENTITY_ID',
            'FIELD_NAME'    => 'USER_FIELD.FIELD_NAME',
            'USER_TYPE_ID'  => 'USER_FIELD.USER_TYPE_ID',
            'XML_ID'        => 'USER_FIELD.XML_ID',
            'SORT'          => 'USER_FIELD.SORT',
            'MULTIPLE'      => 'USER_FIELD.MULTIPLE',
            'MANDATORY'     => 'USER_FIELD.MANDATORY',
            'SHOW_FILTER'   => 'USER_FIELD.SHOW_FILTER',
            'SHOW_IN_LIST'  => 'USER_FIELD.SHOW_IN_LIST',
            'EDIT_IN_LIST'  => 'USER_FIELD.EDIT_IN_LIST',
            'IS_SEARCHABLE' => 'USER_FIELD.IS_SEARCHABLE',
            'SETTINGS'      => 'USER_FIELD.SETTINGS',
        ])
            ->where('LANGUAGE_ID', $lang)
            ->where('USER_FIELD.ENTITY_ID', 'HLBLOCK_' . $id)
            ->exec();

        while ($field = $res->fetch()) {
            $fields[] = [
                'CODE'    => $field['FIELD_NAME'],
                'NAME'    => $field['LIST_COLUMN_LABEL'],
                'FULL_EL' => $field,
            ];
        }

        return $fields;
    }
}
