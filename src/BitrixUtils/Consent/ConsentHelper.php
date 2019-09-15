<?php

namespace Vf92\BitrixUtils\Consent;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Main\UserConsent\Internals\AgreementTable;
use Bitrix\Main\UserConsent\Internals\EO_Agreement;
use InvalidArgumentException;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Exceptions\Config\VersionException;
use Vf92\BitrixUtils\Exceptions\Consent\ConsentNotFoundException;

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
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     * @throws VersionException
     */
    public static function getConsentId($code): int
    {
        if (Version::getInstance()->isVersionLessThan('18.0.4')) {
            throw new VersionException();
        }
        if (empty($code)) {
            throw new InvalidArgumentException('code must be specified');
        }
        $query = AgreementTable::query()->setSelect(['ID']);
        $res = $query->where('CODE', $code)->exec();
        $itemId = 0;
        /** @var EO_Agreement $item */
        $item = $res->fetchObject();
        if ($item !== null) {
            $itemId = $item->getId();
        }
        if ($itemId > 0) {
            return $itemId;
        }
        throw new ConsentNotFoundException(sprintf('Consent `%s` not found', $code));
    }
}