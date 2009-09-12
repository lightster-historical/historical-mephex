<?php


require_once PATH_LIB . 'com/mephex/captcha/Captcha.php';


class MXT_CaptchaImage
{
    protected $captchaId;
    protected $width;
    protected $height;


    protected function __construct($captchaId)
    {
        $this->captchaId = intval($captchaId);

        $this->width = 175;
        $this->height = 50;
    }


    protected function drawImage()
    {
        if($this->captchaId > 0)
        {
            $captcha = MXT_Captcha::getCaptchaUsingId($this->captchaId);

            $img = $this->createImage();
            $this->drawImageWithCaptcha($img, $captcha);
            $this->outputImage($img);
            $this->destroyImage($img);

            exit;
        }
    }

    protected function createImage()
    {
        $img = imagecreate($this->width, $this->height);
        $white = imagecolorallocate($img, 255, 255, 255);
        imagefilledrectangle($img, 0, 0, $this->width - 1, $this->height - 1, $white);

        return $img;
    }

    protected function drawImageWithCaptcha($img, MXT_Captcha $captcha = null)
    {
        $text = '';
        if(!is_null($captcha))
            $text = $captcha->getValue();

        $font = PATH_LIB . 'com/mephex/captcha/Font.ttf';
        $grey = imagecolorallocate($img, 100, 100, 100);

        $size = 20;
        $angle = 0;#rand(-5, 5);
        $box = imagettfbbox($size, $angle, $font, $text);
        $left = ($this->width - ($box[2]-$box[0])) / 2;
        $bottom = ($this->height + ($box[1]-$box[7])) / 2;
        imagettftext($img, $size, $angle, $left, $bottom, $grey, $font, $text);

        $width = $this->width;#$box[2] - $box[0];
        $hCenter = $width / 2;#$box[0] + ($width / 2);
        $leftCenter = ($width / 4);#$box[0] + ($width / 4);
        $rightCenter = (3 * $width / 4);#$box[0] + (3 * $width / 4);

        $height = $this->height;#$box[1] - $box[7];
        $vCenter = $height / 2;#intval($box[7] + ($height / 2));

        $xOffset1 = rand(0, 15);
        $xOffset2 = rand(0, 15);
        $yOffset1 = rand(0, 5);
        $yOffset2 = rand(0, 5);
        imagearc($img, $hCenter, $vCenter + ($height / 2) - $yOffset2, $width - $xOffset1, $height - $yOffset1, 180, 270, $grey);
        imagearc($img, $hCenter, $vCenter - ($height / 2), $width - $xOffset2, -$height + $yOffset2, 270, 360, $grey);

        for($i = 0; $i < 100; $i++)
        {
            imagesetpixel($img, rand(0, $this->width - 1), rand(0, $this->height - 1), $grey);
        }
    }

    protected function outputImage($img)
    {
        if(function_exists('imagegif'))
        {
            header('Content-type: image/gif');
            imagegif($img);
        }
        else if(function_exists('imagepng'))
        {
            header('Content-type: image/png');
            imagepng($img);
        }
        else if(function_exists('imagejpeg'))
        {
            header('Content-type: image/jpeg');
            imagejpeg($img, NULL, 100);
        }
        else if(function_exists('imagewbmp'))
        {
            header('Content-type: image/vnd.wap.wbmp');
            imagewbmp($img);
        }
        else
        {
            die('Image generation failed');
        }
    }

    protected function destroyImage($img)
    {
        imagedestroy($img);
    }



    public static function drawUsingCaptchaId($id)
    {
        $captchaImage = new MXT_CaptchaImage($id);
        $captchaImage->drawImage();
    }
}


?>
