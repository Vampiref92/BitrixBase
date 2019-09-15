<?php

namespace Vf92\BitrixUtils\Decorators;

use Bitrix\Main\Application;
use Bitrix\Main\EO_Site;
use Bitrix\Main\SiteTable;
use Bitrix\Main\SystemException;
use Exception;
use RuntimeException;
use Vf92\BitrixUtils\Config\Version;
use Vf92\BitrixUtils\Exceptions\Config\VersionException;
use Vf92\Log\LoggerFactory;

/**
 * Project specific SvgDecorator
 *
 * @package Vf92\BitrixUtils\Decorators
 */
class FullHrefDecorator
{
    /** @var string Домен */
    private static $host;
    /** @var string Протокол: http|https */
    private static $proto;
    /**
     * @var
     */
    private $path;

    /**
     * FullHrefDecorator constructor.
     *
     * @param string $path
     */
    public function __construct($path)
    {
        $this->setPath($path);
    }

    /**
     * @param $path
     */
    public function setPath($path): void
    {
        $this->path = $path;
    }

    /**
     * @return string
     * @throws Exception
     */
    public function __toString()
    {
        try {
            return $this->getFullPublicPath();
        } catch (SystemException $e) {
            try {
                $logger = LoggerFactory::create('fullHrefDecorator');
                $logger->critical('Системная ошибка при получении пукбличного пути ' . $e->getTraceAsString());
            } catch (RuntimeException $e) {
            }
            return '';
        }
    }

    /**
     * @return string
     * @throws SystemException
     * @throws VersionException
     */
    public function getFullPublicPath(): string
    {
        $prefix = $this->getProto();
        $host = $this->getHost();
        return $prefix . '://' . $host . $this->path;
    }

    /**
     * @return string
     */
    public function getStartPath(): string
    {
        return $this->path;
    }

    /**
     * @return string
     * @throws SystemException
     */
    public function getProto(): string
    {
        if (static::$proto === null) {
            static::$proto = 'http';
            $context = Application::getInstance()->getContext();
            if ($context->getRequest()->isHttps()) {
                static::$proto .= 's';
            }
        }
        return static::$proto;
    }

    /**
     * Сброс значения протокола
     */
    public function flushProto(): void
    {
        static::$proto = null;
    }

    /**
     * @param string $host
     *
     * @return FullHrefDecorator
     */
    public function setHost($host): FullHrefDecorator
    {
        $this::$host = $host;
        return $this;
    }

    /**
     * @return string
     * @throws SystemException
     * @throws VersionException
     */
    public function getHost(): string
    {
        if (static::$host === null) {
            $context = Application::getInstance()->getContext();
            static::$host = $context->getServer()->getHttpHost();
            static::$host = static::$host ? trim(static::$host) : '';
            // в cli нет HTTP_HOST, пробуем через константу
            if (static::$host === '' && defined('SITE_SERVER_NAME')) {
                static::$host = trim(SITE_SERVER_NAME);
            }
            // ... или через сайт
            if (static::$host === '') {
                $query = SiteTable::query()->setOrder(['SORT' => 'ASC']);
                if (Version::getInstance()->isVersionLessThan('18.0.4')) {
                    throw new VersionException();
                }
                $query->where('ACTIVE', 'Y');
                /** @var EO_Site $site */
                $site = $query->exec()->fetchObject();
                if ($site) {
                    static::$host = $site->getServerName();
                }
            }
        }
        return static::$host;
    }

    /**
     * Сброс значения хоста
     */
    public function flushHost(): void
    {
        static::$host = null;
    }
}
