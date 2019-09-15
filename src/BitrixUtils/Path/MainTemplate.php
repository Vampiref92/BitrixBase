<?php namespace Vf92\BitrixUtils\Path;

use function defined;

/**
 * Class MainTemplate
 *
 * Класс для основного шаблона
 *
 * @package Vf92\BitrixUtils\Path
 */
class MainTemplate extends TemplateAbstract
{
    /**
     * @return bool
     */
    public function isIndex(): bool
    {
        return $this->isPage('/');
    }

    /**
     * Страница 404
     *
     * @return bool
     */
    public function is404(): bool
    {
        return defined('ERROR_404') && ERROR_404 === 'Y';
    }

    /**
     * Страница, недоступная для неавторизованных
     *
     * @return bool
     */
    public function isForbidden(): bool
    {
        /**
         * It's bitrix way
         */
        global $USER;

        return defined('NEED_AUTH') && NEED_AUTH === true && !$USER->IsAuthorized();
    }

    /**
     * @return bool
     */
    public function isCatalog(): bool
    {
        return $this->isDir('/catalog');
    }

    /**
     * @return bool
     */
    public function isCatalogPage(): bool
    {
        return $this->isPartitionDir('/catalog');
    }

    /**
     * @return bool
     */
    public function hasCatalogPage(): bool
    {
        return $this->isCatalog() || $this->isCatalogPage();
    }

    /**
     * @return bool
     */
    public function isPersonal(): bool
    {
        return $this->isDir('/personal');
    }

    /**
     * @return bool
     */
    public function isPersonalPage(): bool
    {
        return $this->isPartitionDir('/personal');
    }

    /**
     * @return bool
     */
    public function hasPersonalPage(): bool
    {
        return ($this->isPersonal() || ($this->isPersonalPage() && !$this->isBasket() && !$this->isOrder() && !$this->isFavorite() && !$this->isCompare()));
    }

    /**
     * @return bool
     */
    public function isBasket(): bool
    {
        return $this->isDir('/personal/cart');
    }

    /**
     * @return bool
     */
    public function isOrder(): bool
    {
        return $this->isDir('/personal/order/make');
    }

    /**
     * @return bool
     */
    public function isCompare(): bool
    {
        return $this->isDir('/personal/compare');
    }

    /**
     * @return bool
     */
    public function isFavorite(): bool
    {
        return $this->isDir('/personal/favorite');
    }
}
