<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 14.09.18
 * Time: 0:18
 */

namespace Vf92\Iblock;

use Bitrix\Iblock\PropertyTable;
use Bitrix\Main\Entity\Query;
use Bitrix\Main\Entity\QueryChain;
use Bitrix\Main\Entity\QueryChainElement;
use Bitrix\Main\Entity\ReferenceField;
use Vf92\Constructor\EntityConstructor;

class ExtendsElementBitrixQuery extends Query
{
    private $iblockId;

    public function exec()
    {
        $vals = $this->getSelect();
        if (!empty($vals)) {
            $this->convertProp($vals);
            $this->setSelect($vals);
        }

        $vals = $this->getOrder();
        if (!empty($vals)) {
            $this->convertProp($vals);
            $this->setOrder($vals);
        }

        $vals = $this->getFilter();
        if (!empty($vals)) {
            $this->convertProp($vals);
            $this->setFilter($vals);
        }

        $vals = $this->getGroup();
        if (!empty($vals)) {
            $this->convertProp($vals);
            $this->setGroup($vals);
        }

        $vals = $this->getWhereChains();
        if (!empty($vals)) {
            $this->convertProp($vals);
            $this->where_chains = $vals;
        }

        /** @todo union */
        /** @todo runtime */
//        $this->registerRuntimeField()

        parent::exec();
    }

    public function getIblockId()
    {
        return (int)$this->iblockId;
    }

    public function setIblockId(int $iblockId)
    {
        $this->iblockId = $iblockId;
    }

    protected function convertProp(&$val)
    {
        if (\is_array($val)) {
            $val = $this->convertPropArray($val);
        } else {
            if ($val instanceof QueryChain) {
                $els = $val->getAllElements();
                if (!empty($els)) {
                    $els = $this->convertPropArray($els);
                    $val2 = clone $val;
                    $val2->__construct();
                    foreach ($els as $el) {
                        $val2->addElement($el);
                    }
                }
            } elseif ($val instanceof QueryChainElement) {
                $key = $val->getAliasFragment();
                $this->convertProp($key);
                $value = $val->getValue();
                $this->convertProp($value);
                $val = new QueryChainElement();
            } elseif (\is_string($val)) {
                $propVal = str_replace('PROP.', '', $val);
                if (\is_numeric($propVal)) {
                    $propId = $propVal;
                } else {
                    $propCode = $propVal;
                }
                $propQuery = PropertyTable::query();
                $propQuery->setSelect([
                    'ID',
                    'CODE',
                    'PROPERTY_TYPE',
                    'LIST_TYPE',
                    'MULTIPLE',
                    'LINK_IBLOCK_ID',
                    'USER_TYPE',
                    'VERSION',
                ]);
                if (!empty($propCode)) {
                    $propQuery->where('CODE', $propCode);
                }
                if (!empty($propId)) {
                    $propQuery->where('ID', $propId);
                }
                $propQuery->setCacheTtl(360000);
                $prop = $propQuery->exec()->fetch();
                switch ((int)$prop['VERSION']) {
                    case 1:
                        $propReplaceName = 'IBLOCK_' . $this->getIblockId() . '_PROPERTY_' . $prop['ID'];
                        $this->registerPropVersion1($propReplaceName, $prop);
                        $val = $propReplaceName . '.VALUE';
                        break;
                    case 2:
                        switch ($prop['MULTILE']) {
                            case 'Y':
                                $propReplaceName = 'IBLOCK_' . $this->getIblockId() . '_PROPERTY_MULTIPLE_' . $prop['ID'];
                                $this->registerPropVersion2Multiple($propReplaceName, $prop);
                                $val = $propReplaceName . '.VALUE';
                                break;
                            default:
                                $propReplaceName = 'IBLOCK_' . $this->getIblockId() . '_PROPERTY_SINGLE_' . $prop['ID'];
                                $this->registerPropVersion2($propReplaceName);
                                $val = $propReplaceName . '.PROPERTY_' . $prop['ID'];
                        }
                        break;
                }
            }
        }
    }

    /**
     * @param $propReplaceName
     * @param $prop
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    protected function registerPropVersion1($propReplaceName, $prop)
    {
        $this->registerRuntimeField(
            new ReferenceField(
                $propReplaceName,
                EntityConstructor::compileEntityDataClass('IblockElementProperty',
                    'b_iblock_element_property', [
                        'PROPERTY' => new ReferenceField(
                            'PROPERTY',
                            PropertyTable::getEntity(),
                            ['=this.IBLOCK_PROPERTY_ID' => 'ref.ID']
                        ),
                    ]
                ),
                ['=this.ID' => 'ref.IBLOCK_ELEMENT_ID', 'this.IBLOCK_PROPERTY_ID' => $prop['ID']]
            )
        );
    }

    /**
     * @param $propReplaceName
     * @param $prop
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    protected function registerPropVersion2Multiple($propReplaceName, $prop)
    {
        $this->registerRuntimeField(
            new ReferenceField(
                $propReplaceName,
                (IblockPropEntityConstructor::getMultipleDataClass($this->getIblockId()))::getEntity(),
                [
                    '=this.ID'                => 'ref.IBLOCK_ELEMENT_ID',
                    'this.IBLOCK_PROPERTY_ID' => $prop['ID'],
                ]
            )
        );
    }

    /**
     * @param $propReplaceName
     *
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\SystemException
     */
    protected function registerPropVersion2($propReplaceName)
    {
        $this->registerRuntimeField(
            new ReferenceField(
                $propReplaceName,
                (IblockPropEntityConstructor::getDataClass($this->getIblockId()))::getEntity(),
                ['=this.ID' => 'ref.IBLOCK_ELEMENT_ID']
            )
        );
    }

    /**
     * @param array $val
     *
     * @return array
     */
    protected function convertPropArray(array $val)
    {
        $result = [];
        foreach ($val as $key => $item) {
            if (!\is_numeric($key)) {
                $this->convertProp($key);
            }
            if (!\is_numeric($key)) {
                $this->convertProp($item);
            }
            $result[$key] = $item;
        }
        return $result;
    }
}