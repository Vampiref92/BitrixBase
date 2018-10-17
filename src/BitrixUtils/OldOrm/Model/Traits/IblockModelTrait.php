<?php

namespace Vf92\BitrixUtils\OldOrm\Model\Traits;

trait IblockModelTrait
{

    /**
     * @var int
     */
    protected $IBLOCK_ID = 0;

    /**
     * @var string
     */
    protected $CODE = '';

    /**
     * @var string
     */
    protected $LIST_PAGE_URL = '';

    /**
     * @return int
     */
    public function getIblockId(): int
    {
        return (int)$this->IBLOCK_ID;
    }

    /**
     * @param int $iblockId
     *
     * @return $this
     */
    public function withIblockId(int $iblockId)
    {
        $this->IBLOCK_ID = $iblockId;

        return $this;
    }

    /**
     * @return string
     */
    public function getListPageUrl(): string
    {
        return $this->LIST_PAGE_URL;
    }

    /**
     * @param string $url
     *
     * @return $this
     */
    public function withListPageUrl(string $url)
    {
        $this->LIST_PAGE_URL = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->CODE;
    }

    /**
     * @param string $CODE
     *
     * @return $this
     */
    public function withCode(string $CODE)
    {
        $this->CODE = $CODE;

        return $this;
    }
}
