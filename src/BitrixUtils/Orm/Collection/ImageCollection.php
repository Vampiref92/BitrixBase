<?php

namespace Vf92\BitrixUtils\Orm\Collection;

use Bitrix\Main\FileTable;
use InvalidArgumentException;
use Vf92\BitrixUtils\Orm\Model\Image;
use Vf92\Enum\MediaEnum;
use Vf92\MiscUtils\Collection\ObjectArrayCollection;

/**
 * Class ImageCollection
 * @package Vf92\BitrixUtils\Orm\Collection
 */
class ImageCollection extends ObjectArrayCollection
{
    /**
     * @param array $ids
     *
     * @return ImageCollection
     * @throws \Bitrix\Main\ArgumentException
     * @throws \Bitrix\Main\ObjectPropertyException
     * @throws \Bitrix\Main\SystemException
     */
    public static function createFromIds(array $ids = [])
    {
        if (!empty($ids)) {
            $collection = new static();
            $result = FileTable::query()->addFilter('ID', $ids)->addSelect('*')->exec();
            while ($item = $result->fetch()) {
                $collection->add(new Image($item));
            }
        } else {
            $collection = static::createNoImageCollection();
        }

        return $collection;
    }

    /**
     * Dirty hack
     *
     * @throws \InvalidArgumentException
     */
    public static function createNoImageCollection()
    {
        $collection = new static();

        $collection->add(Image::getNoImage());

        return $collection;
    }

    public function getResizeCollection($size, $resizeType = BX_RESIZE_IMAGE_PROPORTIONAL)
    {
        $collection = new static();
        /** @var Image $item */
        foreach ($this->getIterator() as $item) {
            $collection->add($item->getResizeImage($size, $resizeType));
        }
        return $collection;
    }

    /**
     * @param mixed $object
     *
     * @return void
     * @throws InvalidArgumentException
     */
    protected function checkType($object)
    {
        if (!($object instanceof Image)) {
            throw new InvalidArgumentException('Переданный объект не является картинкой');
        }
    }
}
