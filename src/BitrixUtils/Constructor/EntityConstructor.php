<?php

namespace Vf92\BitrixUtils\Constructor;

use Bitrix\Main;
use Bitrix\Main\Entity\DataManager;
use Vf92\MiscUtils\MiscUtils;

/**
 * Class EntityConstructor
 * @package Vf92\BitrixUtils\Constructor
 */
class EntityConstructor
{
    /**
     * @param string $className
     * @param string $tableName
     * @param array  $additionalFields
     *
     * @return DataManager|string
     * @throws Main\SystemException
     */
    public static function compileEntityDataClass($className, $tableName, array $additionalFields = [])
    {
        $entity_data_class = $className;

        if (!preg_match('/^[a-z0-9_]+$/i', $entity_data_class)) {
            throw new Main\SystemException(
                sprintf(
                    'Invalid entity name `%s`.',
                    $entity_data_class
                )
            );
        }

        $entity_data_class .= 'Table';

        if (class_exists($entity_data_class)) {
            return $entity_data_class;
        }

        $mapOld = static::getFieldsMap($tableName);
        $mapNew = static::getNewFieldsMap($mapOld);
        if (\count($mapOld) === \count($mapNew)) {
            $currentFieldsMap = $mapNew;
        } else {
            $currentFieldsMap = $mapOld;
        }

        $eval = 'use Bitrix\Main;
        
				class ' . $entity_data_class . ' extends Main\Entity\DataManager
				{
					public static function getTableName()
					{
						return ' . var_export($tableName, true) . ';
					}

					public static function getMap()
					{
						return [';
                        $allFields = array_merge($currentFieldsMap, $additionalFields);
                        foreach ($allFields as $key => $val) {
                            if (\is_object($val)) {
                                continue;
                            }
                            if (!\is_numeric($key)) {
                                $eval .= '\'' . $key . '\' => ';
                            }
                            if (\is_array($val)) {
                                $val = var_export($val, true);
                            } else {
                                if (!\is_string($val)) {
                                    $val .= '\'' . $val . '\'';
                                }
                            }
                            $eval .= $val . ', ' . PHP_EOL;
                        }
                        $eval .= '];
					}
				}
			';

        eval($eval);

        return $entity_data_class;
    }

    /**
     * @param $tableName
     *
     * @return array|mixed
     */
    public static function getFieldsMap($tableName)
    {
        /** @todo переделать с массива на классы */
        $fieldsMap = [];
        $obTable = new \CPerfomanceTable;
        $obTable->Init($tableName);

        $arFields = $obTable->GetTableFields(false, true);

        $arUniqueIndexes = $obTable->GetUniqueIndexes();
        $hasID = false;
        foreach ($arUniqueIndexes as $indexName => $indexColumns) {
            if (array_values($indexColumns) === ['ID']) {
                $hasID = $indexName;
            }
        }

        if ($hasID) {
            $arUniqueIndexes = [$hasID => $arUniqueIndexes[$hasID]];
        }

        if (\is_array($arFields) && !empty($arFields)) {
            foreach ($arFields as $columnName => $columnInfo) {
                if ($columnInfo['orm_type'] === 'boolean') {
                    $columnInfo['nullable'] = true;
                    $columnInfo['type'] = 'bool';
                    $columnInfo['length'] = '';
                    $columnInfo['enum_values'] = [
                        'N',
                        'Y',
                    ];
                }

                if ($columnInfo['type'] === 'int'
                    && ($columnInfo['default'] > 0)
                    && !$columnInfo['nullable']) {
                    $columnInfo['nullable'] = true;
                }

                $match = [];
                if (preg_match('/^(.+)_TYPE$/', $columnName, $match)
                    && array_key_exists($match[1], $arFields)
                    && (int)$columnInfo['length'] === 4) {
                    $columnInfo['nullable'] = true;
                    $columnInfo['orm_type'] = 'enum';
                    $columnInfo['enum_values'] = [
                        'text',
                        'html',
                    ];
                }

                $fieldsMap[$columnName]['data_type'] = $columnInfo['orm_type'];

                $primary = false;
                foreach ($arUniqueIndexes as $indexName => $arColumns) {
                    if (\in_array($columnName, $arColumns, true)) {
                        $fieldsMap[$columnName]['primary'] = true;
                        $primary = true;
                        break;
                    }
                }
                if ($columnInfo['increment']) {
                    $fieldsMap[$columnName]['autocomplete'] = true;
                }
                if (!$primary && $columnInfo['nullable'] === false) {
                    $fieldsMap[$columnName]['required'] = true;
                }
                if ($columnInfo['orm_type'] === 'boolean' || $columnInfo['orm_type'] === 'enum') {
                    $fieldsMap[$columnName]['values'] = $columnInfo['enum_values'];
                }
            }
        }

        return $fieldsMap;
    }

    /**
     * @param $fieldsMap
     *
     * @return array
     * @throws Main\SystemException
     */
    public static function getNewFieldsMap($fieldsMap)
    {
        $newFieldsMap = [
        ];
        foreach ($fieldsMap as $columnName => $columnInfo) {
            $params = [
                'autocomplete' => $columnInfo['autocomplete'],
                'required'     => $columnInfo['required'],
                'values'       => $columnInfo['values'],
                'primary'      => $columnInfo['primary'],
            ];
            MiscUtils::eraseArray($params);
            switch ($columnInfo['data_type']) {
                case 'integer':
                    $newFieldsMap[] = 'new Main\Entity\IntegerField(\'' . $columnName . '\', ' . var_export($params,
                            true) . ')';
                    break;
                case 'float':
                    $newFieldsMap[] = 'new Main\Entity\FloatField(\'' . $columnName . '\', ' . var_export($params,
                            true) . ')';
                    break;
                case 'boolean':
                    $newFieldsMap[] = 'new Main\Entity\BooleanField(\'' . $columnName . '\', ' . var_export($params,
                            true) . ')';
                    break;
                case 'date':
                    $newFieldsMap[] = 'new Main\Entity\DateField(\'' . $columnName . '\', ' . var_export($params,
                            true) . ')';
                    break;
                case 'datetime':
                    $newFieldsMap[] = 'new Main\Entity\DateTimeField(\'' . $columnName . '\', ' . var_export($params,
                            true) . ')';
                    break;
                case 'string':
                    if ($columnInfo['length'] > 255) {
                        $newFieldsMap[] = 'new Main\Entity\TextField(\'' . $columnName . '\', ' . var_export($params,
                                true) . ')';
                    } else {
                        $newFieldsMap[] = 'new Main\Entity\StringField(\'' . $columnName . '\', ' . var_export($params,
                                true) . ')';
                    }
                    break;

            }
        }
        return $newFieldsMap;
    }
}