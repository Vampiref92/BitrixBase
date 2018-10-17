<?php

namespace Vf92\BitrixUtils\OldOrm\Collection;

use Adv\Bitrixtools\Collection\ObjectArrayCollection;
use Doctrine\Common\Collections\Collection;
use Vf92\BitrixUtils\OldOrm\Model\Image;
use Vf92\BitrixUtils\OldOrm\Model\ResizeImageDecorator;
use InvalidArgumentException;

class ResizeImageCollection extends ObjectArrayCollection
{
    /**
     * @var int
     */
    private $width;

    /**
     * @var int
     */
    private $height;

    public function __construct(array $objects = [], int $width = 0, int $height = 0)
    {
        parent::__construct($objects);
        $this->width = $width;
        $this->height = $height;
    }

    public static function createFromImageCollection(Collection $imageCollection, int $width = 0, int $height = 0)
    {
        $collection = new ResizeImageCollection([], $width, $height);
        foreach ($imageCollection as $image) {
            $collection->addFromImage($image);
        }
        return $collection;
    }

    /**
     * @return int
     */
    public function getWidth(): int
    {
        return $this->width;
    }

    /**
     * @param int $width
     *
     * @return ResizeImageCollection
     */
    public function setWidth(int $width): ResizeImageCollection
    {
        $this->width = $width;
        $this->forAll(function ($key, ResizeImageDecorator $image) use ($width) {
            $image->setResizeWidth($width);
        });
        return $this;
    }

    /**
     * @return int
     */
    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param int $height
     *
     * @return ResizeImageCollection
     */
    public function setHeight(int $height): ResizeImageCollection
    {
        $this->height = $height;
        $this->forAll(function ($key, ResizeImageDecorator $image) use ($height) {
            $image->setResizeHeight($height);
        });
        return $this;
    }

    /**
     * @param Image $image
     *
     * @throws \InvalidArgumentException
     * @return static
     */
    public function addFromImage(Image $image)
    {
        $isExist = $this->exists(function ($key, ResizeImageDecorator $element) use ($image) {
            return $element->getId() === $image->getId();
        });
    
        if (!$isExist) {
            $resizedImage = new ResizeImageDecorator($image->getFields());
        
            if ($this->getWidth()) {
                $resizedImage->setResizeWidth($this->getWidth());
            }
        
            if ($this->getHeight()) {
                $resizedImage->setResizeHeight($this->getHeight());
            }

            $this->add($resizedImage);
        }
    
        return $this;
    }

    /**
     * @param mixed $object
     *
     * @throws InvalidArgumentException
     * @return void
     */
    protected function checkType($object)
    {
        if (!($object instanceof ResizeImageDecorator)) {
            throw new InvalidArgumentException('Попытка добавить объект не корректного типа в коллекцию');
        }
    }
}
