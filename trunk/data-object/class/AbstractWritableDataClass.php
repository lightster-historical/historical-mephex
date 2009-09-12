<?php


require_once PATH_LIB . 'com/mephex/data-object/class/CacheableDataClass.php';
require_once PATH_LIB . 'com/mephex/data-object/DataObject.php';

require_once PATH_LIB . 'com/mephex/event/SaveListener.php';


abstract class MXT_AbstractWritableDataClass extends MXT_CacheableDataClass
{
    protected $writer;


    public function __construct()
    {
        $this->writer = null;

        parent::__construct();
    }


    protected abstract function getDefaultWriter();

    public function getWriter()
    {
        if(is_null($this->writer))
            $this->writer = $this->getDefaultWriter();

        return $this->writer;
    }


    public function save(MXT_DataObject $obj, MXT_SaveListener $saveListener)
    {
        return $this->saveUsingWriter($obj, $this->getWriter(), $saveListener);
    }

    public function create(MXT_DataObject $obj)
    {
        return $this->createUsingWriter($obj, $this->getWriter());
    }

    public function update(MXT_DataObject $obj)
    {
        return $this->updateUsingWriter($obj, $this->getWriter());
    }
}



?>
