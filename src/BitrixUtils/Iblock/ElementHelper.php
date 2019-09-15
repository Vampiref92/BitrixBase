<?php

namespace Vf92\BitrixUtils\Iblock;

use Bitrix\Iblock\ElementTable;
use Bitrix\Iblock\InheritedProperty\ElementTemplates;
use Bitrix\Iblock\SectionTable;
use Bitrix\Main\ArgumentException;
use Bitrix\Main\Error;
use Bitrix\Main\IO\File;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\Result;
use Bitrix\Main\SystemException;
use CCatalogProduct;
use CCatalogSku;
use CFile;
use CIBlockElement;
use CIBlockProperty;
use CPrice;
use CUtil;
use Exception;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Exceptions\Config\VersionException;
use Vf92\BitrixUtils\Exceptions\Iblock\Element\ElementException;
use Vf92\BitrixUtils\Exceptions\Iblock\Element\ElementNotFoundException;
use Vf92\BitrixUtils\Exceptions\Iblock\Element\ElementOffersCopyException;
use Vf92\BitrixUtils\Exceptions\Iblock\Section\SectionNotFoundException;
use Vf92\BitrixUtils\Exceptions\Orm\OrmQueryException;
use function in_array;

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
     * @throws ElementNotFoundException
     * @throws ElementException
     * @throws VersionException
     * @throws OrmQueryException
     */
    public static function getIdByCode($iblockId, $code): ?int
    {
        try {
            $query = ElementTable::query()->setSelect(['ID']);
            if (Version::getInstance()->isVersionLessThan('18.0.4')) {
                throw new VersionException();
            }
            $query->where('CODE', $code)->where('IBLOCK_ID', $iblockId);
            $id = (int)$query->exec()->fetchObject();
            if ($id <= 0) {
                throw new ElementNotFoundException('элемент не найден');
            }
            return $id;
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new OrmQueryException($e->getMessage());
        }

    }

    /**
     * Check if element exists
     *
     * @param $id
     *
     * @return bool
     * @throws ElementException
     */
    public static function isElementExists($id): bool
    {
        try {
            $queryResult = ElementTable::query()->setSelect(['ID'])->where('ID', $id)->exec();
            return $queryResult->getSelectedRowsCount() > 0;
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new ElementException('ошибка запроса');
        }
    }

    /**
     * @param       $mask
     * @param array $returnColumns
     *
     * @return array
     * @throws ElementException
     */
    public static function getElementsByCodeMask($mask, $returnColumns = ['ID']): array
    {
        $elements = [];
        try {
            $queryResult = ElementTable::query()->setSelect($returnColumns)->whereLike('CODE', $mask)->exec();
            while ($item = $queryResult->fetch()) {
                $elements[] = $item;
            }
        } catch (ObjectPropertyException|ArgumentException|SystemException $e) {
            throw new ElementException('ошибка запроса');
        }
        return $elements;
    }

    /**
     * @param $elementId
     *
     * @return array
     * @throws ArgumentException
     * @throws ElementNotFoundException
     * @deprecated
     */
    public static function getElementFullInfoById($elementId): array
    {
        $elementId = (int)$elementId;
        if ($elementId <= 0) {
            throw new ArgumentException('Идентификатор элемента не является числом, большим 0', 'elementId');
        }
        $currentElementObject = CIBlockElement::GetByID($elementId)->GetNextElement();
        if (!$currentElementObject) {
            throw new ElementNotFoundException('Элемент не найден');
        }
        $currentElementInfo = [];
        $currentElementInfo['FIELDS'] = $currentElementObject->GetFields();
        $currentElementInfo['PROPS'] = $currentElementObject->GetProperties(false, ['EMPTY' => 'N']);
        $currentElementInfo['FIELDS']['PREVIEW_PICTURE'] = (int)$currentElementInfo['FIELDS']['PREVIEW_PICTURE'];
        if ($currentElementInfo['FIELDS']['PREVIEW_PICTURE'] > 0) {
            $currentElementInfo['FIELDS']['PREVIEW_PICTURE'] = CFile::MakeFileArray($currentElementInfo['FIELDS']['PREVIEW_PICTURE']);
            if (empty($currentElementInfo['FIELDS']['PREVIEW_PICTURE']) || !File::isFileExists($currentElementInfo['FIELDS']['PREVIEW_PICTURE']['tmp_name'])) {
                $currentElementInfo['FIELDS']['PREVIEW_PICTURE'] = false;
            } else {
                if (in_array($currentElementInfo['FIELDS']['PREVIEW_PICTURE']['type'],
                    ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'])) {
                    $currentElementInfo['FIELDS']['PREVIEW_PICTURE']['COPY_FILE'] = 'Y';
                } else {
                    $currentElementInfo['FIELDS']['PREVIEW_PICTURE'] = false;
                }
            }
        } else {
            $currentElementInfo['FIELDS']['PREVIEW_PICTURE'] = false;
        }
        $currentElementInfo['FIELDS']['DETAIL_PICTURE'] = (int)$currentElementInfo['FIELDS']['DETAIL_PICTURE'];
        if ($currentElementInfo['FIELDS']['DETAIL_PICTURE'] > 0) {
            $currentElementInfo['FIELDS']['DETAIL_PICTURE'] = CFile::MakeFileArray($currentElementInfo['FIELDS']['DETAIL_PICTURE']);
            if (empty($currentElementInfo['FIELDS']['DETAIL_PICTURE']) || !File::isFileExists($currentElementInfo['FIELDS']['DETAIL_PICTURE']['tmp_name'])) {
                $currentElementInfo['FIELDS']['DETAIL_PICTURE'] = false;
            } else {
                if (in_array($currentElementInfo['FIELDS']['DETAIL_PICTURE']['type'],
                    ['image/jpeg', 'image/png', 'image/gif', 'image/bmp'])) {
                    $currentElementInfo['FIELDS']['DETAIL_PICTURE']['COPY_FILE'] = 'Y';
                } else {
                    $currentElementInfo['FIELDS']['DETAIL_PICTURE'] = false;
                }
            }
        } else {
            $currentElementInfo['FIELDS']['DETAIL_PICTURE'] = false;
        }
        $currentElementInfo['GROUPS'] = [];
        $obElementGroups = CIBlockElement::GetElementGroups($elementId, true, ['ID']);
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
        $offers = CCatalogSku::getOffersList($elementId);
        if (is_array($offers)) {
            reset($offers);
            $currentElementInfo['OFFERS'] = current($offers);
        }
        return $currentElementInfo;
    }

    /**
     * Get seo field templates.
     *
     * @param int  $iblockId  Iblock ID.
     * @param int  $elementId Element ID.
     * @param bool $getAll    Get with inherited.
     *
     * @return array
     */
    public static function getSeoFieldTemplates($iblockId, $elementId, $getAll = false): array
    {
        $result = [];
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
        unset($fieldTemplate, $fieldName, $data);
        return $result;
    }

    /**
     * @param                 $elementId
     * @param bool|array      $sectionIds
     * @param string|int|bool $iblockId
     *
     * @return Result
     * @throws ArgumentException
     * @throws ElementException
     * @throws ElementNotFoundException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws SectionNotFoundException
     */
    public static function copy($elementId, $sectionIds = false, $iblockId = false): Result
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
        $elementSections = [];
        if ($sectionIds !== false) {
            if (!is_array($sectionIds)) {
                throw new ArgumentException('Идентификаторы разделов должны быть переданы в виде массива',
                    'sectionIds');
            }
            foreach ($sectionIds as &$sectionId) {
                $sectionId = (int)$sectionId;
            }
            unset($sectionId);
            $queryResult = SectionTable::query()->setSelect(['ID', 'IBLOCK_ID'])->whereIn('ID', $sectionIds)->exec();
            while ($elementSection = $queryResult->fetch()) {
                if ($iblockId !== false && (int)$elementSection['IBLOCK_ID'] !== (int)$iblockId) {
                    continue;
                }
                $elementSections[] = $elementSection;
            }
            if (empty($elementSections)) {
                throw new SectionNotFoundException('раздел не найден');
            }
            if (!isset($elementIblock)) {
                $elementIblock = IblockHelper::getIblockCodeSettingsById($elementSection['IBLOCK_ID']);
            }
        }
        $currentElement = static::getElementFullInfoById($elementId);
        if (!isset($elementIblock)) {
            $elementIblock = IblockHelper::getIblockCodeSettingsById($currentElement['FIELDS']['IBLOCK_ID']);
        }
        $newElementFields = static::buildNewElementFields($elementIblock, $elementSections, $currentElement);
        $el = new CIBlockElement();
        $newItemId = $el->Add($newElementFields);
        if (!$newItemId) {
            $result->addError(new Error('Ошибка добавления элемента каталога: ' . $el->LAST_ERROR));
            return $result;
        }
        $priceRes = CPrice::GetListEx([], ['PRODUCT_ID' => $elementId], false, false,
            ['PRODUCT_ID', 'EXTRA_ID', 'CATALOG_GROUP_ID', 'PRICE', 'CURRENCY', 'QUANTITY_FROM', 'QUANTITY_TO']);
        while ($arPrice = $priceRes->Fetch()) {
            $arPrice['PRODUCT_ID'] = $newItemId;
            CPrice::Add($arPrice);
        }
        $arProduct = [
            'ID' => $newItemId,
        ];
        $productRes = CCatalogProduct::GetList([], ['ID' => $elementId], false, false, [
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
            'TYPE',
        ]);
        if ($arCurProduct = $productRes->Fetch()) {
            $arProduct = $arCurProduct;
            $arProduct['ID'] = $newItemId;
            if (isset($arProduct['SUBSCRIBE_ORIG'])) {
                $arProduct['SUBSCRIBE'] = $arProduct['SUBSCRIBE_ORIG'];
            }
            foreach ($arProduct as $productKey => $productValue) {
                if ($productValue === null) {
                    unset($arProduct[$productKey]);
                }
            }
        }
        if (!CCatalogProduct::Add($arProduct, false)) {
            $result->addError(new Error('Ошибка добавления параметров товара к элементу каталога'));
            return $result;
        }
        if (!empty($currentElement['OFFERS'])) {
            $newOffers = [];
            foreach ($currentElement['OFFERS'] as $offer) {
                $offerCopyResult = static::copy($offer['ID']);
                if (!$offerCopyResult->isSuccess()) {
                    throw new ElementOffersCopyException('скопировать торговое предложение не удалось - '.implode('; ',$offerCopyResult->getErrorMessages()));
                }
                $offerId = $offerCopyResult->getData()['NEW_ITEM'];
                CIBlockElement::SetPropertyValuesEx($offerId, false, ['CML2_LINK' => $newItemId]);
                $newOffers[] = $offerId;
            }
            if (empty($newOffers)) {
                throw new ElementOffersCopyException('ошибка копирвоания торговых предложений');
            }
        }
        $result->setData(['NEW_ITEM' => $newItemId]);
        return $result;
    }

    /**
     * @param $elementIblock
     * @param $elementSections
     * @param $currentElement
     *
     * @return array
     * @throws ArgumentException
     * @throws ElementException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws Exception
     */
    private static function buildNewElementFields($elementIblock, $elementSections, $currentElement): array
    {
        $newElementFields = [
            'IBLOCK_ID'           => isset($elementIblock) ? $elementIblock['ID'] : $currentElement['FIELDS']['IBLOCK_ID'],
            'ACTIVE'              => $currentElement['FIELDS']['ACTIVE'],
            'ACTIVE_FROM'         => $currentElement['FIELDS']['ACTIVE_FROM'],
            'ACTIVE_TO'           => $currentElement['FIELDS']['ACTIVE_TO'],
            'SORT'                => $currentElement['FIELDS']['SORT'],
            'NAME'                => $currentElement['FIELDS']['~NAME'],
            'PREVIEW_PICTURE'     => $currentElement['FIELDS']['PREVIEW_PICTURE'],
            'PREVIEW_TEXT'        => $currentElement['FIELDS']['~PREVIEW_TEXT'],
            'PREVIEW_TEXT_TYPE'   => $currentElement['FIELDS']['PREVIEW_TEXT_TYPE'],
            'DETAIL_TEXT'         => $currentElement['FIELDS']['~DETAIL_TEXT'],
            'DETAIL_TEXT_TYPE'    => $currentElement['FIELDS']['DETAIL_TEXT_TYPE'],
            'DETAIL_PICTURE'      => $currentElement['FIELDS']['DETAIL_PICTURE'],
            'WF_STATUS_ID'        => $currentElement['FIELDS']['WF_STATUS_ID'],
            'CODE'                => $currentElement['FIELDS']['~CODE'],
            'TAGS'                => $currentElement['FIELDS']['~TAGS'],
            'XML_ID'              => $currentElement['FIELDS']['~XML_ID'],
            'IPROPERTY_TEMPLATES' => !empty($currentElement['IPROPERTY_TEMPLATES']) ? $currentElement['IPROPERTY_TEMPLATES'] : [],
            'PROPERTY_VALUES'     => [],
        ];
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
            if (empty($newElementFields['CODE'])) {
                $newElementFields['CODE'] = CUtil::translit($newElementFields, 'ru',
                    ['replace_space' => '-', 'replace_other' => '-']);
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
                    $newCode = $newElementFields['CODE'] . random_int(0, 20000);
                    $counter++;
                }
                $newElementFields['CODE'] = $newCode;
            }
        }
        if ((int)$newElementFields['IBLOCK_ID'] !== (int)$currentElement['FIELDS']['IBLOCK_ID']) {
            $rsProps = CIBlockProperty::GetList([],
                ['IBLOCK_ID' => $newElementFields['IBLOCK_ID'], 'PROPERTY_TYPE' => 'L']);
            while ($prop = $rsProps->Fetch()) {
                $arValueList = [];
                $arNameList = [];
                $rsValues = CIBlockProperty::GetPropertyEnum($prop['ID']);
                while ($arValue = $rsValues->Fetch()) {
                    $arValueList[$arValue['XML_ID']] = $arValue['ID'];
                    $arNameList[$arValue['ID']] = trim($arValue['VALUE']);
                }
                if (!empty($arValueList)) {
                    $propListCache[$prop['CODE']] = $arValueList;
                }
                if (!empty($arNameList)) {
                    $arNamePropListCache[$prop['CODE']] = $arNameList;
                }
            }
            $rsProps = CIBlockProperty::GetList([],
                ['IBLOCK_ID' => $currentElement['FIELDS']['IBLOCK_ID'], 'PROPERTY_TYPE' => 'L']);
            while ($prop = $rsProps->Fetch()) {
                $arValueList = [];
                $arNameList = [];
                $rsValues = CIBlockProperty::GetPropertyEnum($prop['ID']);
                while ($arValue = $rsValues->Fetch()) {
                    $arValueList[$arValue['ID']] = $arValue['XML_ID'];
                    $arNameList[$arValue['ID']] = trim($arValue['VALUE']);
                }
                if (!empty($arValueList)) {
                    $arOldPropListCache[$prop['CODE']] = $arValueList;
                }
                if (!empty($arNameList)) {
                    $arOldNamePropListCache[$prop['CODE']] = $arNameList;
                }
            }
        }
        foreach ($currentElement['PROPS'] as $prop) {
            if ($prop['USER_TYPE'] === 'HTML') {
                if (is_array($prop['~VALUE'])) {
                    if ($prop['MULTIPLE'] === 'N') {
                        $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = [
                            'VALUE' => [
                                'TEXT' => $prop['~VALUE']['TEXT'],
                                'TYPE' => $prop['~VALUE']['TYPE'],
                            ],
                        ];
                        if ($prop['WITH_DESCRIPTION'] === 'Y') {
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']]['DESCRIPTION'] = $prop['~DESCRIPTION'];
                        }
                    } else {
                        if (!empty($prop['~VALUE'])) {
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = [];
                            foreach ($prop['~VALUE'] as $propValueKey => $propValue) {
                                $oneNewValue = [
                                    'VALUE' => [
                                        'TEXT' => $propValue['TEXT'],
                                        'TYPE' => $propValue['TYPE'],
                                    ],
                                ];
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
                    $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = [];
                    foreach ($prop['VALUE'] as $propValueKey => $file) {
                        if ($file > 0) {
                            $tmpValue = CFile::MakeFileArray($file);
                            if (!is_array($tmpValue)) {
                                continue;
                            }
                            if ($prop['WITH_DESCRIPTION'] === 'Y') {
                                $tmpValue = [
                                    'VALUE'       => $tmpValue,
                                    'DESCRIPTION' => $prop['~DESCRIPTION'][$propValueKey],
                                ];
                            }
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']][] = $tmpValue;
                        }
                    }
                } elseif ($prop['VALUE'] > 0) {
                    $tmpValue = CFile::MakeFileArray($prop['VALUE']);
                    if (is_array($tmpValue)) {
                        if ($prop['WITH_DESCRIPTION'] === 'Y') {
                            $tmpValue = [
                                'VALUE'       => $tmpValue,
                                'DESCRIPTION' => $prop['~DESCRIPTION'],
                            ];
                        }
                        $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = $tmpValue;
                    }
                }
            } elseif ($prop['PROPERTY_TYPE'] === 'L') {
                if (!empty($prop['VALUE_ENUM_ID'])) {
                    if ((int)$newElementFields['IBLOCK_ID'] === (int)$currentElement['FIELDS']['IBLOCK_ID']) {
                        $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = $prop['VALUE_ENUM_ID'];
                    } else {
                        if (isset($propListCache[$prop['CODE']], $arOldPropListCache[$prop['CODE']])) {
                            if (is_array($prop['VALUE_ENUM_ID'])) {
                                $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = [];
                                foreach ($prop['VALUE_ENUM_ID'] as &$intValueID) {
                                    $strValueXmlID = $arOldPropListCache[$prop['CODE']][$intValueID];
                                    if (isset($propListCache[$prop['CODE']][$strValueXmlID])) {
                                        $newElementFields['PROPERTY_VALUES'][$prop['CODE']][] = $propListCache[$prop['CODE']][$strValueXmlID];
                                    } else {
                                        $strValueName = $arOldNamePropListCache[$prop['CODE']][$intValueID];
                                        $intValueKey = array_search($strValueName, $arNamePropListCache[$prop['CODE']],
                                            true);
                                        if ($intValueKey !== false) {
                                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']][] = $intValueKey;
                                        }
                                    }
                                }
                                unset($intValueID);
                                if (empty($newElementFields['PROPERTY_VALUES'][$prop['CODE']])) {
                                    unset($newElementFields['PROPERTY_VALUES'][$prop['CODE']]);
                                }
                            } else {
                                $strValueXmlID = $arOldPropListCache[$prop['CODE']][$prop['VALUE_ENUM_ID']];
                                if (isset($propListCache[$prop['CODE']][$strValueXmlID])) {
                                    $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = $propListCache[$prop['CODE']][$strValueXmlID];
                                } else {
                                    $strValueName = $arOldNamePropListCache[$prop['CODE']][$prop['VALUE_ENUM_ID']];
                                    $intValueKey = array_search($strValueName, $arNamePropListCache[$prop['CODE']],
                                        true);
                                    if ($intValueKey !== false) {
                                        $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = $intValueKey;
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
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = [];
                            foreach ($prop['~VALUE'] as $propValueKey => $propValue) {
                                $newElementFields['PROPERTY_VALUES'][$prop['CODE']][] = [
                                    'VALUE'       => $propValue,
                                    'DESCRIPTION' => $prop['~DESCRIPTION'][$propValueKey],
                                ];
                            }
                        } else {
                            $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = $prop['~VALUE'];
                        }
                    }
                } else {
                    $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = ($prop['WITH_DESCRIPTION'] === 'Y' ? [
                        'VALUE'       => $prop['~VALUE'],
                        'DESCRIPTION' => $prop['~DESCRIPTION'],
                    ] : $prop['~VALUE']);
                }
            } else {
                $newElementFields['PROPERTY_VALUES'][$prop['CODE']] = $prop['~VALUE'];
            }
        }
        return $newElementFields;
    }
}