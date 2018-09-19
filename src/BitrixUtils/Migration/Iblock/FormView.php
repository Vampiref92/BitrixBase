<?php
/**
 * Created by ADV/web-engineering co.
 */

namespace Vf92\BitrixUtils\Migration\Iblock;

use Bitrix\Main\Loader;
use CIBlockProperty;
use CUserOptions;
use Exception;

class FormView
{
    /**
     * Настраивает дизайн формы редактирования элемента ИБ
     *
     * @param $iblockId
     * @param array $data массив вида:
     *
     * 'edit1' => [
     *      'NAME' => 'Название вкладки',
     *       'ITEMS' => [
     *           'ACTIVE' => 'Активность',
     *           'SORT' => '*Сортировка',
     *           'NAME' => '*Название',
     *           'PREVIEW_PICTURE' => '*Иконка',
     *           'PROPERTY_OPTIONS' => 'PROPERTY_OPTIONS'
     *       ],
     *   ],
     *
     * @return bool
     */
    public static function designIblockEditForm($iblockId, array $data)
    {
        $tabs = [];
        $properties = self::getProperties($iblockId);

        $fieldSeparator = "--#--";
        $fieldStartEnd = "--";
        $endTab = ";--";
        $defaultTabCode = "";

        foreach ($data as $tabIndex => $tabData) {
            $tabElements = [];

            $tabCode = $defaultTabCode . $tabIndex;
            $tabName = $tabData['NAME'];
            $tabElements[] = $tabCode . $fieldSeparator . $tabName . $fieldStartEnd;

            foreach ($tabData['ITEMS'] as $fieldCode => $fieldName) {
                if (substr_count($fieldCode, 'PROPERTY_')) {
                    $fieldCode = 'PROPERTY_' . $properties[substr($fieldCode, 9)]['ID'];
                }

                if (substr_count($fieldName, 'PROPERTY_')) {
                    $fieldName = $properties[substr($fieldName, 9)]['NAME'];
                }

                $tabElements[] = $fieldStartEnd . $fieldCode . $fieldSeparator . $fieldName . $fieldStartEnd;
            }

            $tabs[] = join(',', $tabElements) . $endTab;
        }

        CUserOptions::SetOptionsFromArray(
            [
                [
                    'c' => 'form',
                    'n' => 'form_element_' . $iblockId,
                    'd' => 'Y',
                    'v' => ['tabs' => join('', $tabs)],
                ],
            ]
        );

        return true;
    }

    /**
     * Настраивает дизайн формы редактирования секции ИБ
     *
     * @param $iblockId
     * @param array $data массив вида:
     *
     * 'edit1' => [
     *      'NAME' => 'Название вкладки',
     *       'ITEMS' => [
     *           'ACTIVE' => 'Активность',
     *           'SORT' => '*Сортировка',
     *           'NAME' => '*Название',
     *       ],
     *   ],
     *
     * @return bool
     */
    public static function designIblockSectionEditForm($iblockId, array $data)
    {
        $tabs = [];

        $fieldSeparator = "--#--";
        $fieldStartEnd = "--";
        $endTab = ";--";
        $defaultTabCode = "";

        foreach ($data as $tabIndex => $tabData) {
            $tabElements = [];

            $tabCode = $defaultTabCode . $tabIndex;
            $tabName = $tabData['NAME'];
            $tabElements[] = $tabCode . $fieldSeparator . $tabName . $fieldStartEnd;

            foreach ($tabData['ITEMS'] as $fieldCode => $fieldName) {
                $tabElements[] = $fieldStartEnd . $fieldCode . $fieldSeparator . $fieldName . $fieldStartEnd;
            }

            $tabs[] = join(',', $tabElements) . $endTab;
        }

        CUserOptions::SetOptionsFromArray(
            [
                [
                    'c' => 'form',
                    'n' => 'form_section_' . $iblockId,
                    'd' => 'Y',
                    'v' => ['tabs' => join('', $tabs)],
                ],
            ]
        );

        return true;
    }

    /**
     * Настраивает столбцы таблицы разделов ИБ
     *
     * @param string $iblockType
     *
     * @param int $iblockId
     * @param array $data массив вида:
     *
     *
     * [
     *     [columns] => NAME,ID
     *     [by] => timestamp_x
     *     [order] => desc
     *     [page_size] => 20
     *]
     *
     * @return bool
     */
    public static function designIblockSectionList($iblockType, $iblockId, array $data)
    {
        $hash = md5($iblockType . "." . $iblockId);

        CUserOptions::SetOptionsFromArray(
            [
                [
                    'c' => 'list',
                    'n' => 'tbl_iblock_list_' . $hash,
                    'v' => $data,
                ],
            ]
        );

        return true;
    }

    private static function getProperties($iblockId)
    {
        if (!Loader::includeModule('iblock')) {
            throw new Exception('Abort! Install iblock module.');
        }

        $properties = [];

        $db = CIBlockProperty::GetList(['SORT' => 'ASC'], ['ACTIVE' => 'Y', 'IBLOCK_ID' => $iblockId]);
        while ($property = $db->Fetch()) {
            $properties[$property['CODE']] = [
                'ID'   => $property['ID'],
                'NAME' => ($property['IS_REQUIRED'] == 'Y' ? '*' : '') . $property['NAME'],
            ];
        }

        return $properties;
    }
}
