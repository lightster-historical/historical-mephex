<?php


require_once PATH_LIB . 'com/mephex/captcha/Captcha.php';
require_once PATH_LIB . 'com/mephex/captcha/CaptchaImage.php';
require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/form/field/AbstractField.php';
require_once PATH_LIB . 'com/mephex/form/field/constraint/RequiredConstraint.php';
require_once PATH_LIB . 'com/mephex/input/IntegerInput.php';


class MXT_CaptchaField extends MXT_AbstractField
{
    const ERROR_CAPTCHA_INCORRECT = 1;
    const ERROR_FORM = 2;


    protected $captchaId;


    public function __construct($keyname)
    {
        parent::__construct($keyname, new MXT_RequiredConstraint());
    }


    public function setInputs(Input $input)
    {
        $inputs = $input->set($this->getKeyname())
            && $input->set($this->getKeyname() . '_id');

        if($input->set('captchaImage', IntegerInput::getInstance()))
        {
            $captchaId = $input->get('captchaImage');
            MXT_CaptchaImage::drawUsingCaptchaId($captchaId);
        }

        return $inputs;
    }


    public function setValuesUsingInput(Input $input)
    {
        $this->setValue($input->get($this->getKeyname()));
        $this->captchaId = $input->get($this->getKeyname() . '_id');
    }


    public function validateValueUsingIndex($index)
    {
        $captchaId = $this->getValue($index);
        $captchaValue = $this->captchaId;
        if(is_array($captchaValue))
            $captchaValue = $captchaValue[$index];

        $captcha = MXT_Captcha::getCaptchaUsingId($captchaId);

        if(!$this->hasErrors())
        {
            if(is_null($captcha) || !$captcha->checkValue($captchaValue))
                throw new MXT_FieldError(self::ERROR_CAPTCHA_INCORRECT, $this->getErrorStatement('captcha', 'verification-failed'));
                //throw new MXT_FieldError(self::ERROR_CAPTCHA_INCORRECT, 'The entered code does not match the verification code.');
        }

        if(!is_null($captcha))
        {
            $captcha->destroyCaptcha();
            $captcha = null;
        }

        return !$this->hasErrors();
    }

    public function printFieldInputAsHTML($index = null)
    {
        $captcha = MXT_Captcha::generateNewCaptcha();

        ?>
         <img src="<?php echo $_SERVER['PHP_SELF']; ?>?captchaImage=<?php echo $captcha->getId(); ?>" width="175" height="50" alt="Captcha Image" style="margin: 2px 0; border: 1px solid #000000; " />
         <input type="text" name="<?php echo $this->getKeynameWithIndex($index); ?>" value="" />
         <input type="hidden" name="<?php echo $this->getKeyname(); ?>_id<?php echo $this->getFieldIndex($index); ?>" value="<?php echo $captcha->getId(); ?>" />
        <?php
    }

    public function notifyOfFormErrors()
    {
        parent::notifyOfFormErrors();

        if(!$this->hasErrors())
            throw new MXT_FieldError(self::ERROR_FORM, $this->getErrorStatement('captcha', 'form-error'));
            //$this->addError(new MXT_FieldError($this, self::ERROR_FORM, 'Please enter the new verification code.'));
    }
}


?>
