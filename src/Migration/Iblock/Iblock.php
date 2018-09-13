<?php

namespace Vf92\Migration\Iblock;

use Vf92\Exception\IblockNotFoundException;
use Vf92\Exception\IblockPropertyNotFoundException;
use Vf92\Exception\MigrationFailureException;
use Vf92\Tools\Iblock\IblockUtils;
use CIBlock;
use CIBlockProperty;
use CIBlockPropertyEnum;
use CIBlockType;

class Iblock
{
    /**
     * @var CIBlockType
     */
    private $CIBlockType;

    /**
     * @var CIBlock
     */
    private $CIBlock;

    /**
     * @var CIBlockProperty
     */
    private $CIBlockProperty;

    /**
     * @var CIBlockPropertyEnum
     */
    private $CIBlockPropertyEnum;

    /**
     * Создаёт или обновляет тип инфоблоков
     *
     * @param array $fields
     * @param string $name
     *
     * @return string
     * @throws MigrationFailureException
     */
    public function setIblockType(array $fields, $name = "")
    {
        $default = [
            'SECTIONS' => 'Y',
            'IN_RSS'   => 'N',
            'SORT'     => 100,
            'LANG'     => [
                'ru' => [
                    'NAME'         => $name ? $name : $fields['ID'],
                    'SECTION_NAME' => 'Разделы',
                    'ELEMENT_NAME' => 'Элементы',
                ],
                'en' => [
                    'NAME'         => $name ? $name : $fields['ID'],
                    'SECTION_NAME' => 'Sections',
                    'ELEMENT_NAME' => 'Products',
                ],
            ],
        ];

        //Не очень хорошо, если я хочу обновить только одно поле, а мне неявно мержится лишняя информация
        //TODO Улучшить этот мерж. Например, исполнять только при добавлении, но не при обновлении.
        $fields = array_replace_recursive($default, $fields);
        $typeID = $fields['ID'];

        if (IblockUtils::isIblockTypeExists($typeID)) {
            unset($fields['ID']);
            if (!$this->CIBlockType()->Update($typeID, $fields)) {
                throw new MigrationFailureException(
                    sprintf(
                        'Error updating iblock type `%s`: %s',
                        $typeID,
                        $this->CIBlockType()->LAST_ERROR
                    )
                );
            }
        } else {
            if (!$this->CIBlockType()->Add($fields)) {
                throw new MigrationFailureException(
                    sprintf(
                        'Error creating iblock type `%s`: %s',
                        $typeID,
                        $this->CIBlockType()->LAST_ERROR
                    )
                );
            }
        }

        return (string)$typeID;
    }

    /**
     * Создаёт или обновляет инфоблок
     *
     * @param array $fields
     *
     * @return int
     * @throws MigrationFailureException
     */
    public function setIblock(array $fields)
    {
        $default = [
            'IBLOCK_TYPE_ID'   => '',
            'VERSION'          => '2',
            'ACTIVE'           => 'Y',
            'LIST_PAGE_URL'    => '',
            'SECTION_PAGE_URL' => '',
            'DETAIL_PAGE_URL'  => '',
            'SITE_ID'          => ['s1'],
            'GROUP_ID'         => ['2' => 'R'],
            'WORKFLOW'         => 'N',
            'INDEX_SECTION'    => 'Y',
            'INDEX_ELEMENT'    => 'Y',
            'SORT'             => '100',
            'ELEMENT_NAME'     => 'Элемент',
            'ELEMENTS_NAME'    => 'Элементы',
            'ELEMENT_ADD'      => 'Добавить элемент',
            'ELEMENT_EDIT'     => 'Изменить элемент',
            'ELEMENT_DELETE'   => 'Удалить элемент',
            'SECTION_NAME'     => 'Категория',
            'SECTIONS_NAME'    => 'Категории',
            'SECTION_ADD'      => 'Добавить категорию',
            'SECTION_EDIT'     => 'Изменить категорию',
            'SECTION_DELETE'   => 'Удалить категорию',
            'FIELDS'           => [
                'CODE'         => [
                    'IS_REQUIRED' => 'N',
                ],
                'SECTION_CODE' => [
                    'IS_REQUIRED' => 'N',
                ],
            ],
        ];

        $fields = array_replace_recursive($default, $fields);

        try {
            $id = IblockUtils::getIblockId($fields['IBLOCK_TYPE_ID'], $fields['CODE']);

            if (!$this->CIBlock()->Update($id, $fields)) {
                throw new MigrationFailureException(
                    sprintf(
                        'Error updating iblock %s/%s: %s',
                        $fields['IBLOCK_TYPE_ID'],
                        $fields['CODE'],
                        $this->CIBlock()->LAST_ERROR
                    )
                );
            }
        } catch (IblockNotFoundException $exception) {
            $id = $this->CIBlock()->Add($fields);

            if (!$id) {
                throw new MigrationFailureException(
                    sprintf(
                        'Error creating iblock %s/%s: %s',
                        $fields['IBLOCK_TYPE_ID'],
                        $fields['CODE'],
                        $this->CIBlock()->LAST_ERROR
                    )
                );
            }
        }

        return (int)$id;
    }

    /**
     * Создаёт или обновляет свойство инфоблока
     *
     * @param array $fields
     *
     * @return int
     * @throws MigrationFailureException
     */
    public function setProperty(array $fields)
    {
        $default = [
            'ACTIVE'         => 'Y',
            'SORT'           => 100,
            'FILTRABLE'      => 'Y',
            'IS_REQUIRED'    => 'N',
            'MULTIPLE'       => 'N',
            'NAME'           => '',
            'CODE'           => 'BRAND',
            'PROPERTY_TYPE'  => '',
            'USER_TYPE'      => '',
            'SMART_FILTER'   => 'Y',
            'IBLOCK_ID'      => '',
            'LINK_IBLOCK_ID' => '',
            /*'PROPERTY_VALUES' => [
                'XML_ID' => [
                    'XML_ID' => '',
                    'VALUE' => '',
                    'SORT' => 0
                ],
            ]*/
        ];

        $property = array_replace_recursive($default, $fields);

        try {
            $id = IblockUtils::getPropertyId($property['IBLOCK_ID'], $property['CODE']);
            unset($property['SMART_FILTER']);

            if (!$this->CIBlockProperty()->Update($id, $property)) {
                throw new MigrationFailureException(
                    sprintf(
                        'Error updating property `%s` of iblock #%s: %s',
                        $property['CODE'],
                        $property['IBLOCK_ID'],
                        $this->CIBlockProperty()->LAST_ERROR
                    )
                );
            }
        } catch (IblockPropertyNotFoundException $exception) {
            $id = $this->CIBlockProperty()->Add($property);

            if (!$id) {
                throw new MigrationFailureException(
                    sprintf(
                        'Error creating property `%s` for iblock #%s: %s',
                        $property['CODE'],
                        $property['IBLOCK_ID'],
                        $this->CIBlockProperty()->LAST_ERROR
                    )
                );
            }
        }

        if ($property['PROPERTY_TYPE'] == 'L' && $id) {
            $values = [];

            $dbEnumList = $this->CIBlockPropertyEnum()->GetList(
                ['SORT' => 'ASC'],
                [
                    'IBLOCK_ID' => $property['IBLOCK_ID'],
                    'CODE'      => $property['CODE'],
                ]
            );
            while ($value = $dbEnumList->Fetch()) {
                $values[$value['XML_ID']] = $value;
            }

            $values = array_merge((array)$property['PROPERTY_VALUES'], $values);

            foreach ($values as $value) {
                if ($value['ID']) {
                    $eId = $value['ID'];
                    unset($value['ID']);

                    if (!$this->CIBlockPropertyEnum()->Update($eId, $value)) {
                        throw new MigrationFailureException(
                            sprintf(
                                'Error updating enum `%s` for property `%s`',
                                $value['VALUE'],
                                $property['CODE']
                            )
                        );
                    }
                } else {
                    $value['PROPERTY_ID'] = $id;
                    if (!$this->CIBlockPropertyEnum()->Add($value)) {
                        throw new MigrationFailureException(
                            sprintf(
                                'Error creating enum `%s` for property `%s`',
                                $value['VALUE'],
                                $property['CODE']
                            )
                        );
                    }
                }
            }
        }

        return (int)$id;
    }

    /**
     * Удаляет инфоблок
     *
     * @param string $code
     * @param string $type
     * @return bool
     * @throws MigrationFailureException
     */
    public function deleteIblock(string $code, string $type) : bool
    {
        $IblockRes = \CIBlock::GetList([], ['TYPE' => $type, 'CODE' => $code]);
        if ($arIblock = $IblockRes->Fetch()) {
            if (!\CIBlock::Delete($arIblock['ID'])) {
                throw new MigrationFailureException('Unable delete iblock '. $arIblock['ID']);
            }
        } else {
            throw new MigrationFailureException('Unable to find iblock '. $code);
        }

        return true;
    }

    /**
     * @return CIBlockType
     */
    private function CIBlockType()
    {
        if (is_null($this->CIBlockType)) {
            $this->CIBlockType = new CIBlockType();
        }

        return $this->CIBlockType;
    }

    /**
     * @return CIBlock
     */
    private function CIBlock()
    {
        if (is_null($this->CIBlock)) {
            $this->CIBlock = new CIBlock();
        }

        return $this->CIBlock;
    }

    /**
     * @return CIBlockProperty
     */
    private function CIBlockProperty()
    {
        if (is_null($this->CIBlockProperty)) {
            $this->CIBlockProperty = new CIBlockProperty();
        }

        return $this->CIBlockProperty;
    }

    private function CIBlockPropertyEnum()
    {
        if (is_null($this->CIBlockPropertyEnum)) {
            $this->CIBlockPropertyEnum = new CIBlockPropertyEnum();
        }

        return $this->CIBlockPropertyEnum;
    }
}
