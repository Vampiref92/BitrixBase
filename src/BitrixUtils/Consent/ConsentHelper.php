<?php

namespace Vf92\BitrixUtils\Consent;


use Bitrix\Main\UserConsent\Internals\AgreementTable;
use InvalidArgumentException;
use Vf92\BitrixUtils\Concent\Exception\ConsentNotFoundException;
use Vf92\BitrixUtils\Config\Version;

/**
 * Class ConsentHelper
 * @package Vf92\BitrixUtils\Consent
 */
class ConsentHelper
{
    /**
     * @param $code
     *
     * @return int
     * @throws ConsentNotFoundException
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function getConsentId($code){
        if(empty($code)){
            throw new InvalidArgumentException('code must be specified');
        }
        $query = AgreementTable::query()->setSelect(['ID']);
        if (Version::getInstance()->isVersionMoreEqualThan('17.5.2')) {
            $res = $query->where('CODE', $code)->exec();
            $itemId = 0;
            if (Version::getInstance()->isVersionMoreEqualThan('18.0.4')) {
                $item = $res->fetchObject();
                if($item !== null) {
                    $itemId = $item->getId();
                }
            } else {
                $item = $res->fetch();
                $itemId = (int)$item['ID'];
            }
            if($itemId > 0){
                return $itemId;
            }
        } else {
            $item = $query->setFilter(['CODE' => $code])->exec()->fetch();
            if($item !== null){
                return (int)$item['ID'];
            }
        }

        throw new ConsentNotFoundException(
            sprintf(
                'Consent `%s` not found',
                $code
            )
        );
    }
}