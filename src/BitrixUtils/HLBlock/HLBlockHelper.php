<?php

namespace Vf92\BitrixUtils\HLBlock;

use Bitrix\Highloadblock\HighloadBlockTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Loader;
use Bitrix\Main\LoaderException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\ORM\Query\Filter\ConditionTree;
use Bitrix\Main\ORM\Query\Result;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserFieldTable;
use Vf92\BitrixUtils\Constructor\EntityConstructor;
use Vf92\BitrixUtils\Exceptions\HLBlock\HLBlockException;
use Vf92\BitrixUtils\Exceptions\HLBlock\HLBlockFoundMoreThanOneException;
use Vf92\BitrixUtils\Exceptions\HLBlock\HLBlockNotFoundException;
use Vf92\BitrixUtils\Exceptions\Orm\OrmQueryException;

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
     * @return int
     * @throws ArgumentException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     * @throws SystemException
     */
    public static function getIdByName(string $name): int
    {
        $params = [
            'select' => ['ID'],
            'filter' => (new ConditionTree())->where('NAME', $name),
        ];
        $item = static::getHighloadTableRes($params)->fetchObject();
        return $item->getId();
    }

    /**
     * @param array $params
     *
     * @return Result
     * @throws OrmQueryException
     * @throws HLBlockNotFoundException
     * @throws HLBlockFoundMoreThanOneException
     */
    public static function getHighloadTableRes(array $params): Result
    {
        try {
            Loader::includeModule('highloadblock');
            $res = HighloadBlockTable::query()->where($params['filter'])->setSelect($params['select'])->exec();
            if ($res->getSelectedRowsCount() > 1) {
                throw new HLBlockFoundMoreThanOneException();
            }
            if ($res->getSelectedRowsCount() === 0) {
                throw new HLBlockNotFoundException();
            }
            return $res;
        } catch (ObjectPropertyException|SystemException|ArgumentException|LoaderException $e) {
            throw new OrmQueryException($e->getMessage());
        }
    }

    /**
     * Получение ID Хайлоад блока по таблице
     *
     * @param string $name
     *
     * @return int
     * @throws ArgumentException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     * @throws SystemException
     */
    public static function getIdByTableName(string $name): int
    {
        $params = [
            'select' => ['ID'],
            'filter' => (new ConditionTree())->where('TABLE_NAME', $name),
        ];
        $item = static::getHighloadTableRes($params)->fetchObject();
        return $item->getId();
    }

    /**
     * @param array $select
     *
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getHLList(array $select = ['ID', 'NAME']): array
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
    public static function getHLFields(int $id, string $lang = 'ru'): array
    {
        $fields = [];
        $dataManager = EntityConstructor::compileEntityDataClass('UserFieldLang', 'b_user_field_lang',
            ['new Main\ORM\Fields\Relations\Reference(\'USER_FIELD\', Main\UserFieldTable::getEntity(), Main\Entity\Query\Join::on(\'this.USER_FIELD_ID\', \'ref.ID\'))']);
        $query = $dataManager::query();
        $res = $query->setSelect([
            'EDIT_FORM_LABEL',
            'LIST_COLUMN_LABEL',
            'ID'            => 'USER_FIELD.ID',
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
        ])->where('LANGUAGE_ID', $lang)->where('USER_FIELD.ENTITY_ID', 'HLBLOCK_' . $id)->exec();
        while ($field = $res->fetch()) {
            $fields[] = [
                'CODE'    => $field['FIELD_NAME'],
                'NAME'    => $field['LIST_COLUMN_LABEL'],
                'ID'      => $field['ID'],
                'FULL_EL' => $field,
            ];
        }
        return $fields;
    }

    /**
     * @param string|int $val
     * @param string     $hlName
     *
     * @return mixed|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws HLBlockException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     */
    public static function getHlValByIdOrXmlId($val, string $hlName)
    {
        if (!empty($val)) {
            if (is_numeric($val)) {
                return static::getHlValById((int)$val, $hlName);
            }
            if (is_string($val)) {
                return static::getHlValByXmlId((string)$val, $hlName);
            }
        }
        return null;
    }

    /**
     * @param int    $val
     * @param string $hlName
     *
     * @return mixed|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws HLBlockException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     */
    public static function getHlValById(int $val, string $hlName)
    {
        $dataManger = HLBlockFactory::createTableObject($hlName);
        return $dataManger::getById($val)->fetchObject();
    }

    /**
     * @param string $val
     * @param string $hlName
     *
     * @return mixed|null
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws HLBlockException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     */
    public static function getHlValByXmlId(string $val, string $hlName)
    {
        $dataManger = HLBlockFactory::createTableObject($hlName);
        return $dataManger::query()->setSelect(['*'])->where('UF_XML_ID', $val)->fetchObject();
    }

    /**
     * @param string $hlName
     * @param string $fieldsName
     *
     * @return mixed
     * @throws ArgumentException
     * @throws HLBlockFoundMoreThanOneException
     * @throws HLBlockNotFoundException
     * @throws OrmQueryException
     * @throws SystemException
     */
    public static function getHlBlockFieldIdByCode(string $hlName, string $fieldsName)
    {
        $hlId = static::getIdByName($hlName);
        $filed = UserFieldTable::query()
            ->where('ENTITY_ID', 'HLBLOCK_' . $hlId)
            ->where('FIELD_NAME', $fieldsName)
            ->setSelect(['ID'])
            ->exec()
            ->fetchObject();
        return $filed->getId();
    }
}
