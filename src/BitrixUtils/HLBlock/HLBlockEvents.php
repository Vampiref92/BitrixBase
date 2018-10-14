<? namespace Vf92\BitrixUtils\HLBlock;

use Bitrix\Main\Entity;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Vf92\BitrixUtils\Helpers\TaggedCacheHelper;

//use Bitrix\Main;

Loc::loadMessages(__FILE__);

if (!Loader::includeModule('highloadblock')) {
    global $APPLICATION;
    $APPLICATION->ThrowException(Loc::getMessage("NOT_INCLUDE_HL_BLOCK"));
}

//$eventManager->registerEventHandler();

/**
 * Class HLBlockEvents
 * @package Vf92\BitrixUtils\Additional
 */
class HLBlockEvents
{
    protected static $instance;

    /**
     * @return mixed
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param Entity\Event $event
     */
    public function onAfterChangeItem(Entity\Event $event)
    {
        $arParams = $event->getParameters();

        TaggedCacheHelper::clearManagedCache(['hl_block_' . $arParams['HLBLOCK_ID']]);
    }

    /**
     * @param Entity\Event $event
     */
    public function onAfterChangeColumn(Entity\Event $event)
    {
        $arParams = $event->getParameters();

        TaggedCacheHelper::clearManagedCache(['hl_block_' . $arParams['IBLOCK_ID'] . '_fields']);
    }
}