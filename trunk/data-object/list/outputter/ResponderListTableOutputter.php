<?php


require_once PATH_LIB . 'com/mephex/language/Language.php';
require_once PATH_LIB . 'com/mephex/data-object/list/outputter/ListTableOutputter.php';


class MXT_DO_ResponderListTableOutputter extends MXT_DO_ListTableOutputter
{
    protected $responder;


    public function __construct(MXT_DO_AbstractManageResponder $responder)
    {
        $this->responder = $responder;
    }


    public function getResponder()
    {
        return $this->responder;
    }


    public function outputTableHeaderRowExtras(MXT_DO_AbstractList $list)
    {
        if($this->getResponder()->isEditAllowed())
        {
            echo '<th>&nbsp;</th>';
        }
    }


    public function outputListObjectRowExtras(MXT_DO_AbstractList $list, MXT_DataObject $obj)
    {
        if($this->getResponder()->isEditAllowed())
        {
            $class = $list->getDataClass();
            $idField = $class->getIdField();
            $id = $obj->getId();

            $this->outputActionLinkCell($idField->getKeyname(), $id, 'edit');
        }
    }

    public function outputActionLinkCell($fieldKeyname, $id, $action)
    {
        $linkText = MXT_Language::getStatementOrBackup($action . '.title', 'com.mephex.data-object.list.' . $action . '.title');
        echo "<td><a href=\"?$fieldKeyname=$id&amp;action=$action\">$linkText</a></td>";
    }
}



?>
