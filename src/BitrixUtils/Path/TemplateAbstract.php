<?php

namespace Vf92\BitrixUtils\Path;

use Bitrix\Main\Context;
use Bitrix\Main\Context\Culture;
use Bitrix\Main\HttpRequest;
use Bitrix\Main\Response;
use Bitrix\Main\Server;
use Bitrix\Main\Web\Uri;

/**
 * Class TemplateAbstract
 *
 * Класс для управления условиями в шаблонах.
 *
 * Определяется три типа методов:
 *
 * - is... : определяет атомарное условие или группу условий (например, isIndex())
 * - has... : композиция условий типа is..., определяет наличие блока в шаблоне. Не должен содержать никакой логики,
 *            помимо вызова методов is и условных операторов
 * - get... : получение чего-либо, используемого в шаблоне.
 *
 * @package Vf92\BitrixUtils\Path
 */
abstract class TemplateAbstract
{

    /**
     * @var
     */
    protected static $instance;

    /**
     * @var Context
     */
    private          $context;

    /**
     * @var string
     */
    private          $path;

    /**
     * @var bool|string|null
     */
    private          $dir;
    
    /**
     * @param Context $context
     *
     * @return static
     */
    public static function getInstance(Context $context): TemplateAbstract
    {
        if (!static::$instance) {
            static::$instance = new static($context);
        }
        
        return static::$instance;
    }
    
    /**
     * TemplateAbstract constructor.
     *
     * @param Context $context
     */
    protected function __construct(Context $context)
    {
        $this->context = $context;
        
        $uri        = $this->getUri();
        $this->path = $uri->getPath();
        $this->dir  = $context->getRequest()->getRequestedPageDirectory();
    }
    
    /**
     * Находимся на странице $page
     *
     * @param string $page
     *
     * @return bool
     */
    public function isPage($page): bool
    {
        return $this->path === $page;
    }

    /**
     * @param $src
     *
     * @return bool
     */
    public function isPartitionPage($src): bool
    {
        return preg_match(sprintf('~%s~', $src), $this->getPath()) > 0;
    }
    
    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
    
    /**
     * @return bool
     */
    public function isAjaxRequest(): bool
    {
        $server = $this->getServer();
        
        return $server->get('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest' || $server->get('HTTP_BX_AJAX') === 'true';
    }
    
    /**
     * @return HttpRequest
     */
    public function getRequest(): HttpRequest
    {
        return $this->context->getRequest();
    }
    
    /**
     * @return Uri
     */
    public function getUri(): Uri
    {
        return new Uri($this->getRequest()->getRequestUri());
    }
    
    /**
     * @return Server
     */
    public function getServer(): Server
    {
        return $this->context->getServer();
    }
    
    /**
     * @return Culture
     */
    public function getCulture(): Culture
    {
        return $this->context->getCulture();
    }
    
    /**
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->context->getResponse();
    }

    /**
     * @return bool|string|null
     */
    public function getDir()
    {
        return $this->dir;
    }

    /**
     * @param $src
     *
     * @return bool
     */
    public function isPartitionDir($src): bool
    {
        return preg_match(sprintf('~^%s/[-/@\w]+~', $src), $this->getDir()) > 0;
    }

    /**
     * @param $dir
     *
     * @return bool
     */
    public function isDir($dir): bool
    {
        return $this->dir === $dir;
    }

    /**
     * @param $src
     *
     * @return bool
     */
    public function isPartitionDirByFilePath($src): bool
    {
        return preg_match(sprintf('~^%s/[-/@\w]+~', $src), $this->getServer()->getScriptName()) > 0;
    }
}
