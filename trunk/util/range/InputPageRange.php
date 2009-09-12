<?php


require_once PATH_LIB . 'com/mephex/core/Input.php';
require_once PATH_LIB . 'com/mephex/core/PageRange.php';
require_once PATH_LIB . 'com/mephex/input/IntegerInput.php';


class MXT_InputPageRange extends MXT_PageRange
{
    public function __construct($itemCount, $itemsPerPage, $currPageInput)
    {
        $input = new Input($_REQUEST);

        $currPage = 0;
        if($input->set($currPageInput, IntegerInput::getInstance()))
            $currPage = $input->get($currPageInput);

        parent::__construct($itemCount, $itemsPerPage, $currPage);
    }
}


?>
