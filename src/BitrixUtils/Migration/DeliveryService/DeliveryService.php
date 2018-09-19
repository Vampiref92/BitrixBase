<?php

namespace Vf92\BitrixUtils\Migration\DeliveryService;

use Vf92\BitrixUtils\Exception\MigrationFailureException;
use Vf92\BitrixUtils\BitrixUtils;
use Bitrix\Main\Loader;
use Bitrix\Sale\Delivery\ExtraServices;
use Bitrix\Sale\Delivery\Services;
use Bitrix\Sale\Delivery\Services\Table as ServicesTable;

class DeliveryService
{
    private $id;

    private $service;

    public function __construct(int $id = 0)
    {
        Loader::includeModule('sale');

        $this->id = $id;
        $this->service = $this->get();
    }

    public function set($fields)
    {
        $service = Services\Manager::createObject($fields);

        if ($service) {
            $fields = $service->prepareFieldsForSaving($fields);
        }

        $ID = $this->service['ID'];

        if ($ID > 0) {
            $res = Services\Manager::update($this->service['ID'], $fields);

            if (!$res->isSuccess()) {
                throw new MigrationFailureException('Update service error: ' . BitrixUtils::extractErrorMessage($res));
            }
        } else {
            $res = Services\Manager::add($fields);

            if ($res->isSuccess()) {
                $ID = $res->getId();
                if (array_key_exists('CODE', $fields)) {
                    $updateResult = ServicesTable::update($ID, ['CODE' => $fields['CODE']]);
                    if (!$updateResult->isSuccess()) {
                        throw new MigrationFailureException(implode(',', $updateResult->getErrorMessages()));
                    }
                }

                /** @noinspection PhpUndefinedMethodInspection */
                if (!$fields["CLASS_NAME"]::isInstalled()) {
                    /** @noinspection PhpUndefinedMethodInspection */
                    $fields["CLASS_NAME"]::install();
                }
            } else {
                throw new MigrationFailureException('Add service error: ' . BitrixUtils::extractErrorMessage($res));
            }
        }

        if ($ID > 0) {
            $setStoresRes = ExtraServices\Manager::setStoresUnActive($ID);

            if (!$setStoresRes->isSuccess()) {
                throw new MigrationFailureException(
                    'Set stores error: ' . BitrixUtils::extractErrorMessage($setStoresRes)
                );
            }
        }

        $this->id = $ID;
        $this->service = $this->get();

        return $this->service;
    }

    public function delete()
    {
        if ($this->service['ID']) {
            $res = Services\Manager::delete($this->service['ID']);

            if (!$res->isSuccess()) {
                throw new MigrationFailureException('Delete service error: ' . BitrixUtils::extractErrorMessage($res));
            }

            $this->service = null;
        }
    }

    private function get()
    {
        if ($this->id > 0) {
            $dbResultList = ServicesTable::GetList(['filter' => ['ID' => $this->id]]);
            $this->service = $dbResultList->fetch();
        }

        return $this->service;
    }
}
