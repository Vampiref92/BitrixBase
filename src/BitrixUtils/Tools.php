<?php

namespace Vf92\BitrixUtils;

use CFile;
use function is_array;
use function is_numeric;

/**
 * Class Tools
 * @package Vf92\BitrixUtils
 */
class Tools
{
    /**
     * @param       array $list
     * @param array $params
     *
     * @return array
     */
    public static function resizeImageList(array $list, array $params = []): array
    {
        if (empty($list) || !is_array($list) || !isset($params['sizes'])) {
            return $list;
        }
        if (!isset($params['resizeType'])) {
            $params['resizeType'] = BX_RESIZE_IMAGE_PROPORTIONAL;
        }
        foreach ($list as $key => $item) {
            $fileId = static::getFileId($item, $params);
            $file = null;
            if ($fileId > 0) {
                $file = static::resizeImage($fileId, $params);
            }
            $list[$key] = static::setImageToList($file, $item, $params);
        }
        return $list;
    }

    /**
     * @param       array $list
     * @param array $params
     *
     * @return array
     */
    public static function getImageList(array $list, array $params = []): array
    {
        foreach ($list as $key => $item) {
            $fileId = static::getFileId($item, $params);
            $file = null;
            if ($fileId > 0) {
                $file = CFile::GetFileArray($fileId);
                if (is_array($file)) {
                    $file = array_change_key_case($file, CASE_UPPER);
                }
            }
            $list[$key] = static::setImageToList($file, $list[$key], $params);
        }
        return $list;
    }

    /**
     * @param       $file
     * @param array $params
     *
     * @return null|array
     */
    public static function resizeImage($file, array $params): ?array
    {
        $fileId = is_array($file) ? (int)$file['ID'] : (int)$file;
        $resizeFile = CFile::ResizeImageGet($file,
            [
                'width' => is_numeric($params['sizes']['width']) ? (int)$params['sizes']['width'] : 0,
                'height' => is_numeric($params['sizes']['height']) ? (int)$params['sizes']['height'] : 0,
            ],
            $params['resizeType'] ?: BX_RESIZE_IMAGE_PROPORTIONAL,
            $params['initSizes'] ?: false,
            $params['filters'] ?: false,
            $params['immediate'] ?: false,
            $params['jpgQuality'] ?: false
        );
        $resizeFile['orig_id'] = $fileId;
        return is_array($resizeFile) ? array_change_key_case($resizeFile, CASE_UPPER) : null;
    }

    /**
     * @param array $images
     * @param bool  $resizeWithBase
     */
    public static function showPictureHtml(array $images, $resizeWithBase = false): void
    {
        echo static::getPictureHtml($images, $resizeWithBase);
    }

    /**
     * @param array $images
     * @param bool  $resizeWithBase
     *
     * @return string
     */
    public static function getPictureHtml(array $images, $resizeWithBase = false): string
    {
        if (empty($images) || !is_array($images)) {
            return '';
        }

        $real = null;
        foreach ($images as $key => $image) {
            if ($image['real']) {
                $real = $image;
                unset($images[$key]);
            }
        }
        /** если не нашли то берем первый */
        if (empty($real)) {
            reset($images);
            $real = current($images);
        }

        /** @todo получать id по пути если он не задан - это запрос к базе но универсальней + кеширование влепить с кешем привязанным к таблице файлов */
//        if(empty($real['id'])){
//            $real['id'] =
//        }

        if (isset($real['resize']) && !empty($real['id'])) {
            $file = static::resizeImage($real['id'], [
                'sizes' => $real['resize']['sizes'],
                'resizeType' => $real['resize']['type'],
                'filters' => $real['filters'] ?: false
            ]);
            $real['src'] = $file['SRC'];
        }

        if ($resizeWithBase) {
            foreach ($images as &$image) {
                $file = static::resizeImage($real['id'], [
                    'sizes' => $image['resize']['sizes'],
                    'resizeType' => $image['resize']['type'],
                    'filters' => $image['filters'] ?: false
                ]);
                $image['src'] = $file['SRC'];
            }
            unset($image);
        }

        $html = '<picture>';
        if (!empty($images)) {
            foreach ($images as $image) {
                if (!$resizeWithBase && isset($image['resize']) && !empty($image['id'])) {
                    $file = static::resizeImage($image['id'], [
                        'sizes' => $image['resize']['sizes'],
                        'resizeType' => $image['resize']['type'],
                        'filters' => $image['filters'] ?: false
                    ]);
                    $image['src'] = $file['SRC'];
                }
                $html .= static::getSourceHtml($image);
            }
        }
        $html .= static::getSourceHtml($real);
        $html .= '<img src="' .
            $real['src'] . '"' .
            ($real['alt'] ? ' alt="' . $real['alt'] . '"' : '') .
            ($real['title'] ? ' alt="' . $real['title'] . '"' : '') .
            ($real['class'] ? ' class="' . $real['class'] . '"' : '') .
            ($real['additional'] ? ' ' . $real['additional'] : '') .
            '>';
        $html .= '</picture>';
        return $html;
    }

    /**
     * @param $image
     *
     * @return string
     */
    /**
     * @param $image
     *
     * @return string
     */
    /**
     * @param $image
     *
     * @return string
     */
    /**
     * @param $image
     *
     * @return string
     */
    protected static function getSourceHtml($image): string
    {
        return '<source
                        ' . ($image['media'] ? ' media="' . $image['media'] . '"' : '') . '
                        sizes="' . ($image['sizes'] ?: '100vw') . '"
                        srcset="' . $image['src'] . '"
                        type="' . ($image['mime'] ?: CFile::GetContentType($image['src'])) . '">';
    }

    /**
     * @param array|null $file
     * @param            $el
     * @param array $params
     *
     * @return array
     */
    protected static function setImageToList($file, $el, array $params): array
    {
        if (isset($params['imgKeyNew'])) {
            if (!is_array($el)) {
                $el = [];
            }
            if ($params['imgKey'] !== false && $params['subPath'] === true) {
                $el[$params['imgKey']][$params['imgKeyNew']] = $file;
            } else {
                $el[$params['imgKeyNew']] = $file;
            }
        } else {
            if ($params['imgKey'] !== false) {
                if (!is_array($el)) {
                    $el = [];
                }
                $el[$params['imgKey']] = $file;
            } else {
                $el = $file;
            }
        }
        if (!is_array($el)) {
            $el = null;
        }
        return $el;
    }

    /**
     * @param       $item
     * @param array $params
     *
     * @return int
     */
    protected static function getFileId($item, array $params): int
    {
        if ($params['imgKey'] !== false) {
            $fileId = $item[$params['imgKey']]['ID'] ?: $item[$params['imgKey']];
        } else {
            $fileId = $item['ID'] ?: $item;
        }
        return (int)$fileId;
    }
}