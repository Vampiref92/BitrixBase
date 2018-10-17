<?php

namespace Vf92\BitrixUtils\OldOrm\Collection;

use FourPaws\App\Application;
use Vf92\BitrixUtils\OldOrm\Model\CatalogProduct;
use JMS\Serializer\DeserializationContext;

class CatalogProductCollection extends CdbResultCollectionBase
{
    private $serializer;

    public function __construct(\CDBResult $result)
    {
        parent::__construct($result);
        /** @noinspection PhpUnhandledExceptionInspection */
        $this->serializer = Application::getInstance()->getContainer()->get('jms_serializer');
    }

    /**
     * Извлечение модели
     */
    protected function fetchElement(): \Generator
    {
        while ($fields = $this->getCdbResult()->Fetch()) {
            yield $this->serializer->fromArray(
                $fields,
                CatalogProduct::class,
                DeserializationContext::create()->setGroups(['read'])
            );
        }
    }
}
