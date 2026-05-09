<?php
/**
 * Created by: Andrey Polyakov (andrey@polyakov.im)
 */

namespace Travelpayouts\helpers;

use Travelpayouts;

class FileHelper
{
    /**
     * Отдаем содержание файла по алиасу
     * @param string $path
     * @return false|string
     */
    public static function requireAssetByAlias(string $path): string
    {
        try {
            $assetPath = Travelpayouts::getAlias($path);
            if (file_exists($assetPath)) {
                return file_get_contents($assetPath);
            }
        } catch (\Exception $e) {
        }

        return '';

    }

    public static function requireSvgByAlias(string $path, $htmlOptions = [])
    {


    }
}