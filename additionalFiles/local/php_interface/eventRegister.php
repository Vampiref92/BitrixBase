<?php

use Bitrix\Main\EventManager;

$eventManager = EventManager::getInstance();

//$eventManager->addEventHandlerCompatible();
//$eventManager->addEventHandler();

\Vf92\Iblock\ElementOrm::query()->where('PROPERTY.CODE')->exec();