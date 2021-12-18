<?php
namespace App\Lib;

use \Intervention\Image\Image;

class Skinlib
{
    /* Допустимые пропорции образа */

    const SKIN_BASE = 64;
    const SKIN_PROP = 2; // 64 / 32

    /*
     * Массив допустимых пропорций плаща (для плаща в MC нет четкой привязки к размеру)
     * Некоторые плащи используют соотношение 22x17, тогда как обычно используется
     * соотношение 64x32 с незаполненным пространством
     */

    private static $cloakProps = array(
        0 => array('base' => 64, 'ratio' => 2),
        1 => array('base' => 22, 'ratio' => 1.29),
    );

    /**
     * Создает изображение головы; вид спереди
     * @param Image $image
     * @param int $size размер возвращаемого изображения в пикселях
     * @return Image
     */

    public static function createHeadImage(Image $image, $size = 151)
    {
        $info = self::isValidSkinImage($image);

        $av = imagecreatetruecolor($size, $size);
        $mp = $info['scale'];

        $im = imagecreatefromstring($image->getEncoded());

        imagecopyresized($av, $im, 0, 0, 8 * $mp, 8 * $mp, $size, $size, 8 * $mp, 8 * $mp);
        imagecopyresized($av, $im, 0, 0, 40 * $mp, 8 * $mp, $size, $size, 8 * $mp, 8 * $mp);
        imagedestroy($im);

        return \Image::make($av);
    }

    /**
     * Создать видовое изображение из скина; фронтальный \ задний вид
     * @param Image $skin_image
     * @param Image|boolean $cloak_image
     * @param string|boolean $side вид спереди - front \ вид сзади - back \ по умолчанию оба вида на одном изображении последовательно
     * @param int $size высота возвращаемого изображения в пикселях (ширина пропорцианальна задаваемой высоте и завист так же от параметра $side)
     * @return Image|boolean
     */

    public static function createPreviewImage(Image $skin_image, $cloak_image, $side = false, $size = 224)
    {
        if (!$info = self::isValidSkinImage($skin_image)){
            //\Log::error('PREVIEW ERROR 1');
            return false;
        }


        $skin = @imagecreatefromstring($skin_image->encode('png'));
        if (!$skin){
            //\Log::error('PREVIEW ERROR 2');
            return false;
        }

        $mp = $info['scale'];
        $size_x = (($side) ? 16 : 32);
        $preview = imagecreatetruecolor($size_x * $mp, 32 * $mp);
        $mp_x_h = ($side) ? 0 : imagesx($preview) / 2;

        $transparent = imagecolorallocatealpha($preview, 255, 255, 255, 127);
        imagefill($preview, 0, 0, $transparent);

        if (!$side or $side === 'front') {

            imagecopy($preview, $skin, 4 * $mp, 0 * $mp, 8 * $mp, 8 * $mp, 8 * $mp, 8 * $mp);
            imagecopy($preview, $skin, 0 * $mp, 8 * $mp, 44 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            self::imageflip($preview, $skin, 12 * $mp, 8 * $mp, 44 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            imagecopy($preview, $skin, 4 * $mp, 8 * $mp, 20 * $mp, 20 * $mp, 8 * $mp, 12 * $mp);
            imagecopy($preview, $skin, 4 * $mp, 20 * $mp, 4 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            self::imageflip($preview, $skin, 8 * $mp, 20 * $mp, 4 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            imagecopy($preview, $skin, 4 * $mp, 0 * $mp, 40 * $mp, 8 * $mp, 8 * $mp, 8 * $mp);
        }
        if (!$side or $side === 'back') {

            imagecopy($preview, $skin, $mp_x_h + 4 * $mp, 8 * $mp, 32 * $mp, 20 * $mp, 8 * $mp, 12 * $mp);
            imagecopy($preview, $skin, $mp_x_h + 4 * $mp, 0 * $mp, 24 * $mp, 8 * $mp, 8 * $mp, 8 * $mp);
            self::imageflip($preview, $skin, $mp_x_h + 0 * $mp, 8 * $mp, 52 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            imagecopy($preview, $skin, $mp_x_h + 12 * $mp, 8 * $mp, 52 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            self::imageflip($preview, $skin, $mp_x_h + 4 * $mp, 20 * $mp, 12 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            imagecopy($preview, $skin, $mp_x_h + 8 * $mp, 20 * $mp, 12 * $mp, 20 * $mp, 4 * $mp, 12 * $mp);
            imagecopy($preview, $skin, $mp_x_h + 4 * $mp, 0 * $mp, 56 * $mp, 8 * $mp, 8 * $mp, 8 * $mp);
        }

        if ($cloak_image && $info = self::isValidCloakImage($cloak_image)) {
            $mp_cloak = $info['scale'];
            $cloak = @imagecreatefromstring($cloak_image->encode('png'));

            if ($cloak && isset($mp_cloak)) {
                if ($mp_cloak > $mp) { // cloak bigger
                    $mp_x_h = ($side) ? 0 : ($size_x * $mp_cloak) / 2;
                    $mp_result = $mp_cloak;
                } else {
                    $mp_x_h = ($side) ? 0 : ($size_x * $mp) / 2;
                    $mp_result = $mp;
                }

                $preview_cloak = imagecreatetruecolor($size_x * $mp_result, 32 * $mp_result);
                $transparent = imagecolorallocatealpha($preview_cloak, 255, 255, 255, 127);
                imagefill($preview_cloak, 0, 0, $transparent);

                // ex. copy front side of cloak to new image

                if (!$side or $side === 'front')
                    imagecopyresized(
                        $preview_cloak, // result image
                        $cloak, // source image
                        round(3 * $mp_result), // start x point of result
                        round(8 * $mp_result), // start y point of result
                        round(12 * $mp_cloak), // start x point of source img
                        round(1 * $mp_cloak), // start y point of source img
                        round(10 * $mp_result), // result <- width ->
                        round(16 * $mp_result), // result /|\ height \|/
                        round(10 * $mp_cloak), // width of cloak img (from start x \ y)
                        round(16 * $mp_cloak) // height of cloak img (from start x \ y)
                    );

                imagecopyresized($preview_cloak, $preview, 0, 0, 0, 0, imagesx($preview_cloak), imagesy($preview_cloak), imagesx($preview), imagesy($preview));

                if (!$side or $side === 'back')
                    imagecopyresized(
                        $preview_cloak,
                        $cloak,
                        $mp_x_h + 3 * $mp_result,
                        round(8 * $mp_result),
                        round(1 * $mp_cloak),
                        round(1 * $mp_cloak),
                        round(10 * $mp_result),
                        round(16 * $mp_result),
                        round(10 * $mp_cloak),
                        round(16 * $mp_cloak)
                    );

                $preview = $preview_cloak;
            }
        }

        $size_x = ($side) ? $size / 2 : $size;
        $fullsize = imagecreatetruecolor($size_x, $size);

        imagesavealpha($fullsize, true);
        $transparent = imagecolorallocatealpha($fullsize, 255, 255, 255, 127);
        imagefill($fullsize, 0, 0, $transparent);

        imagecopyresized($fullsize, $preview, 0, 0, 0, 0, imagesx($fullsize), imagesy($fullsize), imagesx($preview), imagesy($preview));

        imagedestroy($preview);
        imagedestroy($skin);
        if ($cloak_image){
            if (isset($cloak)) imagedestroy($cloak);
        }

        return \Image::make($fullsize);
    }

    /**
     * Проверить, является ли файл изображением, с соответствующими для скина пропорциями
     * @param Image $image
     * @return array Если файл не проходит проверку возвращает <b>false</b>, иначе возвращает массив пропорций изображения
     */

    public static function isValidSkinImage(Image $image)
    {
        $imageSize = [
            $image->getWidth(),
            $image->getHeight()
        ];

        return array(
            'ratio' => self::getRatio($imageSize),
            'scale' => self::getScale($imageSize, self::SKIN_BASE),
        );
    }

    /**
     * Проверить, является ли файл изображением, с соответствующими для плащя пропорциями
     * @param Image $image
     * @return array|boolean Если файл не проходит проверку возвращает <b>false</b>, иначе возвращает массив пропорций изображения
     */

    public static function isValidCloakImage(Image $image)
    {
        $imageSize = [
            $image->getWidth(),
            $image->getHeight()
        ];

        for ($i = 0; $i < sizeof(self::$cloakProps); $i++) {
            if (round(self::$cloakProps[$i]['ratio'], 2) != self::getRatio($imageSize))
                continue;

            return array(
                'ratio' => self::$cloakProps[$i]['ratio'],
                'scale' => self::getScale($imageSize, self::$cloakProps[$i]['base']),
            );
        }
        return false;
    }

    private static function getScale($inputImg, $size)
    {
        return $inputImg[0] / $size;
    }

    private static function getRatio($inputImg)
    {
        return round($inputImg[0] / $inputImg[1], 2);
    }

    private static function imageflip(&$result, &$img, $rx = 0, $ry = 0, $x = 0, $y = 0, $size_x = null, $size_y = null)
    {
        if ($size_x < 1)
            $size_x = imagesx($img);
        if ($size_y < 1)
            $size_y = imagesy($img);

        imagecopyresampled($result, $img, $rx, $ry, ($x + $size_x - 1), $y, $size_x, $size_y, 0 - $size_x, $size_y);
    }
}