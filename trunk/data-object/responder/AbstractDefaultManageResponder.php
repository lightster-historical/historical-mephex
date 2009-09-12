<?php


require_once 'path.php';



require_once PATH_LIB . 'com/mephex/data-object/form/ResponderForm.php';
require_once PATH_LIB . 'com/mephex/data-object/list/ResponderList.php';
require_once PATH_LIB . 'com/mephex/data-object/responder/AbstractManageResponder.php';


abstract class MXT_DO_AbstractDefaultManageResponder extends MXT_DO_AbstractManageResponder
{
    protected $siteResponder;


    public function init($args)
    {
        $responder = $this->getSiteResponder();
        if(!is_null($responder))
            $responder->init($args);

        parent::init($args);
    }


    public function get($args)
    {
        $responder = $this->getSiteResponder();

        if(!is_null($responder))
            $responder->printHeader();

        parent::get($args);

        if(!is_null($responder))
            $responder->printFooter();
    }


    protected function getDefaultSiteResponder()
    {
        return null;
    }

    public function getSiteResponder()
    {
        if(is_null($this->siteResponder))
            $this->siteResponder = $this->getDefaultSiteResponder();

        return $this->siteResponder;
    }


    protected function getFormInstance()
    {
        return new MXT_DO_ResponderForm($this, $_SERVER['PHP_SELF'], $this->getId());
    }

    protected function getListInstance()
    {
        return new MXT_DO_ResponderList($this, $this->getItemsPerPage());
    }
}



?>
