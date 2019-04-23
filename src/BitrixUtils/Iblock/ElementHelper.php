<?php


namespace Vf92\BitrixUtils\Iblock;


use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\InheritedProperty\ElementTemplates;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Iblock\Exception\ElementCopyException;
use Vf92\BitrixUtils\Iblock\Exception\ElementNotFoundException;
use Vf92\BitrixUtils\Iblock\Exception\IblockNotFoundException;
use Vf92\BitrixUtils\Iblock\Exception\SectionNotFoundException;

/**
 * Class ElementHelper
 * @package Vf92\BitrixUtils\Iblock
 */
class ElementHelper
{
    /**
     * @param        $iblockId
     * @param string $code
     *
     * @return int|null
     */
    public static function getIdByCode($iblockId, $code)
    {
        //SetFilter т.к. минимальная версия 16.5
        $id = 0;
        try {
            $query = ElementTable::query()->setSelect(['ID']);
            if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
                $query->where('CODE', $code)
                    ->where('IBLOCK_ID', $iblockId);
            } else {
                $query->setFilter(['=CODE' => $code, '=IBLOCK_ID' => $iblockId]);
            }
            $id = (int)$query->exec()->fetch()['ID'];
        } catch (ObjectPropertyException $e) {
            return null;
        } catch (ArgumentException $e) {
            return null;
        } catch (SystemException $e) {
            return null;
        }
        return $id > 0 ? $id : null;
    }

    /**
     * Check if element exists
     *
     * @param $id
     * @return bool
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function isElementExists($id)
    {
        $queryResult = ElementTable::query()
            ->setSelect(['ID'])
            ->where('ID', $id)
            ->exec();
        if (!$queryResult->fetch()) {
            return false;
        }
        return true;
    }

    /**
     * @param $mask
     * @param array $returnColumns
     * @return array
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public static function getElementsByCodeMask($mask, $returnColumns = ['ID'])
    {
        $elements = [];
        $queryResult = ElementTable::query()
            ->setSelect($returnColumns)
            ->whereLike('CODE', $mask)
            ->exec();
        while ($item = $queryResult->fetch()) {
            $elements[] = $item;
        }
        return $elements;
    }

    /**
     * @param $elementId
     * @return array
     * @throws ArgumentException
     * @throws ElementNotFoundException
     * @deprecated
     */
    public static function getElementFullInfoById($elementId)
    {
        $elementId = (int)$elementId;
        if ($elementId <= 0) {
            throw new ArgumentException('Идентификатор элемента не является числом, большим 0', 'elementId');
        }
        $currentElementObject = \CIBlockElement::GetByID($elementId)->GetNextElement();
        if (!$currentElementObject) {
            throw new ElementNotFoundException();
        }
        $currentElementInfo = [];
        $currentElementInfo['FIELDS'] = $currentElementObject->GetFields();
        $currentElementInfo['PROPS'] = $currentElementObject->GetProperties(false, array('EMPTY' => 'N'));

        $currentElementInfo['FIELDS']['PREVIEW_PICTURE'] = (int)$currentElementInfo['FIELDS']['PREVIEW_PICTURE'];
        if ($currentElementInfo['FIELDS']['PREVIEW_PICTURE'] > 0) {
            $currentElementInfo['FIELDS']['PREVIEW_PICTURE'] = \CFile::MakeFileArray($currentElementInfo['FIELDS']['PREVIEW_PICTURE']);
            if (empty($currentElementInfo['FIELDS']['PREVIEW_PICTURE'])) {
                $currentElementInfo['FIELDS']['PREVIEW_PICTURE'] = false;
            } else {
                $currentElementInfo['FIELDS']['PREVIEW_PICTURE']['COPY_FILE'] = 'Y';
            }
        } else {
            $currentElementInfo['FIELDS']['PREVIEW_PICTURE'] = false;
        }
        $currentElementInfo['FIELDS']['DETAIL_PICTURE'] = (int)$currentElementInfo['FIELDS']['DETAIL_PICTURE'];
        if ($currentElementInfo['FIELDS']['DETAIL_PICTURE'] > 0) {
            $currentElementInfo['FIELDS']['DETAIL_PICTURE'] = \CFile::MakeFileArray($currentElementInfo['FIELDS']['DETAIL_PICTURE']);
            if (empty($currentElementInfo['FIELDS']['DETAIL_PICTURE'])) {
                $currentElementInfo['FIELDS']['DETAIL_PICTURE'] = false;
            } else {
                $currentElementInfo['FIELDS']['DETAIL_PICTURE']['COPY_FILE'] = 'Y';
            }
        } else {
            $currentElementInfo['FIELDS']['DETAIL_PICTURE'] = false;
        }
        $currentElementInfo['GROUPS'] = [];
        $obElementGroups = \CIBlockElement::GetElementGroups($elementId, true, ['ID']);
        while ($arElementGroup = $obElementGroups->Fetch()) {
            $currentElementInfo['GROUPS'][] = $arElementGroup['ID'];
        }
        if (empty($currentElementInfo['GROUPS'])) {
            unset($currentElementInfo['GROUPS']);
        }

        $seoTemplates = static::getSeoFieldTemplates($currentElementInfo['FIELDS']['IBLOCK_ID'], $elementId, true);
        if (!empty($seoTemplates)) {
            $currentElementInfo['IPROPERTY_TEMPLATES'] = $seoTemplates;
        }
        unset($seoTemplates);
        return $currentElementInfo;
    }

    /**
     * Get seo field templates.
     *
     * @param int $iblockId Iblock ID.
     * @param int $elementId Element ID.
     * @param bool $getAll Get with inherited.
     * @return array
     */
    public static function getSeoFieldTemplates($iblockId, $elementId, $getAll = false)
    {
        $result = array();

        $getAll = ($getAll === true);
        $seoTemplates = new ElementTemplates($iblockId, $elementId);
        $elementTemplates = $seoTemplates->findTemplates();
        if (empty($elementTemplates) || !is_array($elementTemplates)) {
            return $result;
        }
        foreach ($elementTemplates as &$fieldTemplate) {
            if (!$getAll && (!isset($fieldTemplate['INHERITED']) || $fieldTemplate['INHERITED'] !== 'N')) {
                continue;
            }
            $result[$fieldTemplate['CODE']] = $fieldTemplate['TEMPLATE'];
        }
        unset($fieldName, $data);

        return $result;
    }

    /**
     * @param $elementId
     * @param bool|array $sectionIds
     * @param string|int|bool $iblockId
     * @return Result
     * @throws ArgumentException
     * @throws ElementNotFoundException
     * @throws Exception\IblockFieldSettingsException
     * @throws IblockNotFoundException
     * @throws ObjectPropertyException
     * @throws SectionNotFoundException
     * @throws SystemException
     * @throws ElementCopyException
     */
    public static function copy($elementId, $sectionIds = false, $iblockId = false)
    {
        $result = new Result();
        $elementId = (int)$elementId;
        if ($elementId <= 0) {
            throw new ArgumentException('Идентификатор элемента не является числом, большим 0', 'elementId');
        }
        if ($iblockId !== false) {
            $iblockId = (int)$iblockId;
            $elementIblock = IblockHelper::getIblockCodeSettingsById($iblockId);
        }
        if ($sectionIds !== false) {
            if (!is_array($sectionIds)) {
                throw new ArgumentException('Идентификаторы разделов должны быть переданы в виде массива', 'sectionIds');
            }
            foreach ($sectionIds as &$sectionId) {
                $sectionId = (int)$sectionId;
            }
            unset($sectionId);
            $queryResult = SectionTable::query()
                ->setSelect(['ID', 'IBLOCK_ID'])
                ->whereIn('ID', $sectionIds)
                ->exec();
            $elementSections = [];
            while ($elementSection = $queryResult->fetch()) {
                if ($iblockId !== false && (int)$elementSection['IBLOCK_ID'] !== (int)$iblockId) {
                    continue;
                }
                $elementSections[] = $elementSection;
            }
            if (empty($elementSections)) {
                throw new SectionNotFoundException();
            }
            if (!isset($elementIblock)) {
                $elementIblock = IblockHelper::getIblockCodeSettingsById($elementSection['IBLOCK_ID']);
            }
        }
        $currentElement = static::getElementFullInfoById($elementId);
        $newElementFields = static::buildNewElementFields($elementIblock, $elementSections, $currentElement);

        $el = new \CIBlockElement();
        $newItemId = $el->Add($newElementFields);
        if (!$newItemId) {
            throw new ElementCopyException();
        }
        $arProduct = array(
            'ID' => $newItemId
        );
        $productRes = \CCatalogProduct::GetList(
            array(),
            array('ID' => $elementId),
            false,
            false,
            array(
                'QUANTITY_TRACE_ORIG',
                'CAN_BUY_ZERO_ORIG',
                'NEGATIVE_AMOUNT_TRACE_ORIG',
                'SUBSCRIBE_ORIG',
                'WEIGHT',
                'PRICE_TYPE',
                'RECUR_SCHEME_TYPE',
                'RECUR_SCHEME_LENGTH',
                'TRIAL_PRICE_ID',
                'WITHOUT_ORDER',
                'SELECT_BEST_PRICE',
                'VAT_ID',
                'VAT_INCLUDED',
                'WIDTH',
                'LENGTH',
                'HEIGHT',
                'PURCHASING_PRICE',
                'PURCHASING_CURRENCY',
                'MEASURE',
                'TYPE'
            )
        );
        if ($arCurProduct = $productRes->Fetch()){
            $arProduct = $arCurProduct;
            $arProduct['ID'] = $newItemId;
            $arProduct['QUANTITY'] = 0;
            $arProduct['QUANTITY_TRACE'] = $arProduct['QUANTITY_TRACE_ORIG'];
            $arProduct['CAN_BUY_ZERO'] = $arProduct['CAN_BUY_ZERO_ORIG'];
            $arProduct['NEGATIVE_AMOUNT_TRACE'] = $arProduct['NEGATIVE_AMOUNT_TRACE_ORIG'];
            if (isset($arProduct['SUBSCRIBE_ORIG'])) {
                $arProduct['SUBSCRIBE'] = $arProduct['SUBSCRIBE_ORIG'];
            }
            foreach ($arProduct as $productKey => $productValue) {
                if ($productValue === null)
                    unset($arProduct[$productKey]);
            }
        }
        \CCatalogProduct::Add($arProduct, false);
        return $result;
    }

    /**
     * @param $elementIblock
     * @param $elementSections
     * @param $currentElement
     * @return array
     * @throws ArgumentException
     * @throws Exception\IblockFieldSettingsException
     * @throws IblockNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    private static function buildNewElementFields($elementIblock, $elementSections, $currentElement)
    {
        $newElementFields = array(
            'IBLOCK_ID' => isset($elementIblock) ? $elementIblock['ID'] : $currentElement['FIELDS']['IBLOCK_ID'],
            'ACTIVE' => $currentElement['FIELDS']['ACTIVE'],
            'ACTIVE_FROM' => $currentElement['FIELDS']['ACTIVE_FROM'],
            'ACTIVE_TO' => $currentElement['FIELDS']['ACTIVE_TO'],
            'SORT' => $currentElement['FIELDS']['SORT'],
            'NAME' => $currentElement['FIELDS']['~NAME'],
            'PREVIEW_PICTURE' => $currentElement['FIELDS']['PREVIEW_PICTURE'],
            'PREVIEW_TEXT' => $currentElement['FIELDS']['~PREVIEW_TEXT'],
            'PREVIEW_TEXT_TYPE' => $currentElement['FIELDS']['PREVIEW_TEXT_TYPE'],
            'DETAIL_TEXT' => $currentElement['FIELDS']['~DETAIL_TEXT'],
            'DETAIL_TEXT_TYPE' => $currentElement['FIELDS']['DETAIL_TEXT_TYPE'],
            'DETAIL_PICTURE' => $currentElement['FIELDS']['DETAIL_PICTURE'],
            'WF_STATUS_ID' => $currentElement['FIELDS']['WF_STATUS_ID'],
            'CODE' => $currentElement['FIELDS']['~CODE'],
            'TAGS' => $currentElement['FIELDS']['~TAGS'],
            'XML_ID' => $currentElement['FIELDS']['~XML_ID'],
            'IPROPERTY_TEMPLATES' => $currentElement['IPROPERTY_TEMPLATES'],
            'PROPERTY_VALUES' => []
        );
        $newElementFields['IBLOCK_SECTION'] = false;
        if (!empty($elementSections)) {
            $newElementFields['IBLOCK_SECTION'] = [];
            foreach ($elementSections as $elementSection) {
                $newElementFields['IBLOCK_SECTION'][] = $elementSection['ID'];
            }
        } elseif (!empty($currentElement['GROUPS'])) {
            $newElementFields['IBLOCK_SECTION'] = $currentElement['GROUPS'];
        }
        if (!isset($elementIblock)) {
            $elementIblock = IblockHelper::getIblockCodeSettingsById($newElementFields['IBLOCK_ID']);
        }
        if ($elementIblock['CODE']['IS_REQUIRED'] === 'Y') {
            if (!strlen($newElementFields['CODE'])) {
                $newElementFields['CODE'] = \CUtil::translit($newElementFields, 'ru', ['replace_space' => '-', 'replace_other' => '-']);
            }
        }
        if ($elementIblock['CODE_SETTINGS']['UNIQUE'] === 'Y') {
            $similarCodeElements = static::getElementsByCodeMask($newElementFields['CODE'] . '%', ['ID', 'CODE']);
            if (!empty($similarCodeElements)) {
                $codes = [];
                foreach ($similarCodeElements as $similarCodeElement) {
                    $codes[] = $similarCodeElement['CODE'];
                }
                $counter = 0;
                $newCode = $newElementFields['CODE'];
                while (in_array($newCode, $codes, true) && $counter < 10000) {
                    $newCode = $newElementFields['CODE'] . mt_rand(0, 20000);
                    $counter++;
                }
                $newElementFields['CODE'] = $newCode;
            }
        }
        if ((int)$newElementFields['IBLOCK_ID'] !== (int)$currentElement['FIELDS']['IBLOCK_ID']) {
            $rsProps = \CIBlockProperty::GetList(
                array(),
                array('IBLOCK_ID' => $newElementFields['IBLOCK_ID'], 'PROPERTY_TYPE' => 'L', 'CHECK_PERMISSIONS' => 'N')
            );
            while ($arProp = $rsProps->Fetch()) {
                $arValueList = array();
                $arNameList = array();
                $rsValues = \CIBlockProperty::GetPropertyEnum($arProp['ID']);
                while ($arValue = $rsValues->Fetch()) {
                    $arValueList[$arValue['XML_ID']] = $arValue['ID'];
                    $arNameList[$arValue['ID']] = trim($arValue['VALUE']);
                }
                if (!empty($arValueList)) {
                    $arPropListCache[$arProp['CODE']] = $arValueList;
                }
                if (!empty($arNameList)) {
                    $arNamePropListCache[$arProp['CODE']] = $arNameList;
                }
            }
            $rsProps = \CIBlockProperty::GetList(
                array(),
                array('IBLOCK_ID' => $currentElement['FIELDS']['IBLOCK_ID'], 'PROPERTY_TYPE' => 'L', 'ACTIVE' => 'Y', 'CHECK_PERMISSIONS' => 'N')
            );
            while ($arProp = $rsProps->Fetch()) {
                $arValueList = array();
                $arNameList = array();
                $rsValues = \CIBlockProperty::GetPropertyEnum($arProp['ID']);
                while ($arValue = $rsValues->Fetch()) {
                    $arValueList[$arValue['ID']] = $arValue['XML_ID'];
                    $arNameList[$arValue['ID']] = trim($arValue['VALUE']);
                }
                if (!empty($arValueList)) {
                    $arOldPropListCache[$arProp['CODE']] = $arValueList;
                }
                if (!empty($arNameList)) {
                    $arOldNamePropListCache[$arProp['CODE']] = $arNameList;
                }
            }
        }
        foreach ($currentElement['PROPS'] as $prop) {
            if ($prop['USER_TYPE'] === 'HTML') {
                if (is_array($prop['~VALUE'])) {
                    if ($prop['MULTIPLE'] === 'N') {
                        $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = ['VALUE' => ['TEXT' => $prop['~VALUE']['TEXT'], 'TYPE' => $prop['~VALUE']['TYPE']]];
                        if ($prop['WITH_DESCRIPTION'] === 'Y') {
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']]['DESCRIPTION'] = $prop['~DESCRIPTION'];
                        }
                    } else {
                        if (!empty($prop['~VALUE'])) {
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = [];
                            foreach ($prop['~VALUE'] as $propValueKey => $propValue) {
                                $oneNewValue = ['VALUE' => ['TEXT' => $propValue['TEXT'], 'TYPE' => $propValue['TYPE']]];
                                if ($prop['WITH_DESCRIPTION'] === 'Y') {
                                    $oneNewValue['DESCRIPTION'] = $prop['~DESCRIPTION'][$propValueKey];
                                }
                                $newElementFields['PROPERTY_VALUES'][$prop['CODE']][] = $oneNewValue;
                            }
                        }
                    }
                }
            } elseif ($prop['PROPERTY_TYPE'] === 'F') {
                if (is_array($prop['VALUE'])) {
                    $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = array();
                    foreach ($prop['VALUE'] as $propValueKey => $file) {
                        if ($file > 0) {
                            $tmpValue = \CFile::MakeFileArray($file);
                            if (!is_array($tmpValue))
                                continue;
                            if ($prop['WITH_DESCRIPTION'] === 'Y') {
                                $tmpValue = array(
                                    'VALUE' => $tmpValue,
                                    'DESCRIPTION' => $prop['~DESCRIPTION'][$propValueKey]
                                );
                            }
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']][] = $tmpValue;
                        }
                    }
                } elseif ($prop['VALUE'] > 0) {
                    $tmpValue = \CFile::MakeFileArray($prop['VALUE']);
                    if (is_array($tmpValue)) {
                        if ($prop['WITH_DESCRIPTION'] === 'Y') {
                            $tmpValue = array(
                                'VALUE' => $tmpValue,
                                'DESCRIPTION' => $prop['~DESCRIPTION']
                            );
                        }
                        $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = $tmpValue;
                    }
                }
            } elseif ($prop['PROPERTY_TYPE'] === 'L') {
                if (!empty($arProp['VALUE_ENUM_ID'])) {
                    if ((int)$newElementFields['IBLOCK_ID'] === (int)$currentElement['FIELDS']['IBLOCK_ID']) {
                        $newElementFields['PROPERTY_VALUES'][$arProp['CODE']] = $arProp['VALUE_ENUM_ID'];
                    } else {
                        if (isset($arPropListCache[$arProp['CODE']]) && isset($arOldPropListCache[$arProp['CODE']])) {
                            if (is_array($arProp['VALUE_ENUM_ID'])) {
                                $newElementFields['PROPERTY_VALUES'][$arProp['CODE']] = array();
                                foreach ($arProp['VALUE_ENUM_ID'] as &$intValueID) {
                                    $strValueXmlID = $arOldPropListCache[$arProp['CODE']][$intValueID];
                                    if (isset($arPropListCache[$arProp['CODE']][$strValueXmlID])) {
                                        $newElementFields['PROPERTY_VALUES'][$arProp['CODE']][] = $arPropListCache[$arProp['CODE']][$strValueXmlID];
                                    } else {
                                        $strValueName = $arOldNamePropListCache[$arProp['CODE']][$intValueID];
                                        $intValueKey = array_search($strValueName, $arNamePropListCache[$arProp['CODE']]);
                                        if ($intValueKey !== false) {
                                            $newElementFields['PROPERTY_VALUES'][$arProp['CODE']][] = $intValueKey;
                                        }
                                    }
                                }
                                if (isset($intValueID)) {
                                    unset($intValueID);
                                }
                                if (empty($newElementFields['PROPERTY_VALUES'][$arProp['CODE']])) {
                                    unset($newElementFields['PROPERTY_VALUES'][$arProp['CODE']]);
                                }
                            } else {
                                $strValueXmlID = $arOldPropListCache[$arProp['CODE']][$arProp['VALUE_ENUM_ID']];
                                if (isset($arPropListCache[$arProp['CODE']][$strValueXmlID])) {
                                    $newElementFields['PROPERTY_VALUES'][$arProp['CODE']] = $arPropListCache[$arProp['CODE']][$strValueXmlID];
                                } else {
                                    $strValueName = $arOldNamePropListCache[$arProp['CODE']][$arProp['VALUE_ENUM_ID']];
                                    $intValueKey = array_search($strValueName, $arNamePropListCache[$arProp['CODE']]);
                                    if ($intValueKey !== false) {
                                        $newElementFields['PROPERTY_VALUES'][$arProp['CODE']] = $intValueKey;
                                    }
                                }
                            }
                        }
                    }
                }
            } elseif ($prop['PROPERTY_TYPE'] === 'S' || $prop['PROPERTY_TYPE'] === 'N') {
                if ($prop['MULTIPLE'] === 'Y') {
                    if (is_array($prop['~VALUE'])) {
                        if ($prop['WITH_DESCRIPTION'] === 'Y') {
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = array();
                            foreach ($prop['~VALUE'] as $propValueKey => $propValue) {
                                $newElementFields['PROPERTY_VALUES'][$prop['CODE']][] = array(
                                    'VALUE' => $propValue,
                                    'DESCRIPTION' => $prop['~DESCRIPTION'][$propValueKey]
                                );
                            }
                        } else {
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = $prop['~VALUE'];
                        }
                    }
                } else {
                    $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = (
                    $prop['WITH_DESCRIPTION'] === 'Y'
                        ? ['VALUE' => $prop['~VALUE'], 'DESCRIPTION' => $prop['~DESCRIPTION']]
                        : $prop['~VALUE']
                    );
                }
            } else {
                $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = $prop['~VALUE'];
            }
        }
        return $newElementFields;
    }
}