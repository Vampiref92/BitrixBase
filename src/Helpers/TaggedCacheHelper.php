<?php

namespace Vf92\Helpers;

use Bitrix\Main\Application;
use Bitrix\Main\Data\TaggedCache;
use Bitrix\Main\SystemException;

/**
 * Class TaggedCacheHelper
 *
 * @package Vf92\Helpers
 */
class TaggedCacheHelper
{
    /** @var TaggedCache */
    protected $tagCacheInstance;

    public function __construct($cachePath = '')
    {
        $this->start($cachePath);
    }

    /**
     * @return TaggedCache|null
     */
    public static function getTagCacheInstance()
    {
        $tagCache = null;
        if (\defined('BX_COMP_MANAGED_CACHE')) {
            try {
                $tagCache = Application::getInstance()->getTaggedCache();

            } catch (SystemException $e) {
                /** скипаем и возвращаем null */
            }
        }

        return $tagCache;
    }

    /**
     * @param array            $tags
     * @param TaggedCache|null $tagCache
     */
    public static function addManagedCacheTags(array $tags, TaggedCache $tagCache = null)
    {
        if (!\defined('BX_COMP_MANAGED_CACHE')) {
            return;
        }

        if ($tagCache === null) {
            $tagCache = static::getTagCacheInstance();
        }

        foreach ($tags as $tag) {
            static::addManagedCacheTag($tag, $tagCache);
        }
    }

    /**
     * @param array $tags
     */
    public static function clearManagedCache(array $tags)
    {
        if (!\defined('BX_COMP_MANAGED_CACHE')) {
            return;
        }

        $tagCache = static::getTagCacheInstance();
        if ($tagCache === null) {
            return;
        }

        foreach ($tags as $tag) {
            try {
                $tagCache->clearByTag($tag);
            } catch (\Exception $e) {
//                static::getLogger()->error(sprintf('%s: %s', \get_class($e), $e->getMessage()));
            }
        }
    }

    /**
     * @param string           $tag
     * @param TaggedCache|null $tagCache
     */
    public static function addManagedCacheTag($tag, TaggedCache $tagCache = null)
    {
        if (!\defined('BX_COMP_MANAGED_CACHE')) {
            return;
        }

        if ($tagCache === null) {
            $tagCache = static::getTagCacheInstance();
        }

        $tagCache->registerTag($tag);
    }

    /**
     * @param string $cachePath
     *
     * @return TaggedCacheHelper
     */
    public function start($cachePath = '')
    {
        $this->tagCacheInstance = static::getTagCacheInstance();
        if ($this->tagCacheInstance !== null) {
            $this->tagCacheInstance->startTagCache($cachePath);
        }
        return $this;
    }

    public function end()
    {
        if ($this->tagCacheInstance !== null) {
            $this->tagCacheInstance->endTagCache();
        }
    }

    /**
     * @param array $tags
     *
     * @return TaggedCacheHelper
     */
    public function addTags(array $tags)
    {
        if ($this->tagCacheInstance === null) {
            $this->start();
        }
        static::addManagedCacheTags($tags, $this->tagCacheInstance);
        return $this;
    }

    /**
     * @param string $tag
     *
     * @return TaggedCacheHelper
     */
    public function addTag($tag)
    {
        if ($this->tagCacheInstance === null) {
            $this->start();
        }
        static::addManagedCacheTag($tag, $this->tagCacheInstance);
        return $this;
    }

    public function abortTagCache()
    {
        $this->tagCacheInstance->abortTagCache();
    }
}
