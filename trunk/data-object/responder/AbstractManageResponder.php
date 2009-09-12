<?php


require_once PATH_LIB . 'com/mephex/data-object/DataField.php';
require_once PATH_LIB . 'com/mephex/data-object/filter/form/DefaultFilterForm.php';
require_once PATH_LIB . 'com/mephex/data-object/filter/form/outputter/DefaultFilterFormOutputter.php';
require_once PATH_LIB . 'com/mephex/data-object/list/outputter/ResponderListTableOutputter.php';
require_once PATH_LIB . 'com/mephex/framework/HttpResponder.php';
require_once PATH_LIB . 'com/mephex/form/outputter/DescriptiveFormOutputter.php';

require_once PATH_LIB . 'com/mephex/language/Language.php';

require_once PATH_LIB . 'com/mephex/event/Event.php';
require_once PATH_LIB . 'com/mephex/event/SaveListener.php';


abstract class MXT_DO_AbstractManageResponder extends HttpResponder implements MXT_SaveListener
{
    protected $form;
    protected $list;

    protected $id;
    protected $obj;


    public function __construct()
    {
    }


    public function init($args)
    {
        parent::init($args);

        $this->form = null;
        $this->list = null;

        $this->id = null;
        $this->obj = null;
        
        $input = $this->getInput();
        $input->set('action');

        if($this->getForm()->getDataClass()
            != $this->getList()->getDataClass())
            die('Yell and scream! The form and list data classes do not match.');

    }


    public abstract function getDataClass();

    protected abstract function getFormInstance();
    protected abstract function getListInstance();

    public abstract function getFormLanguageGroup();
    public abstract function getListLanguageGroup();


    public function isEditAllowed()
    {
        return $this->isWriteAllowed();
    }

    public function isCreateAllowed()
    {
        return $this->isWriteAllowed();
    }

    public function isWriteAllowed()
    {
        return $this->isReadAllowed();
    }

    public function isReadAllowed()
    {
        return true;
    }


    public function getId()
    {
        if(is_null($this->id))
        {
            $class = $this->getDataClass();
            $idKeyname = $class->getIdField()->getKeyname();

            if($this->input->set($idKeyname, IntegerInput::getInstance()))
                $this->id = $this->input->get($idKeyname);
            else
                $this->id = -1;
        }

        return $this->id;
    }

    public function getDataObject()
    {
        if(is_null($this->obj))
        {
            $class = $this->getDataClass();

            $input = $this->getInput();
            $id = $this->getId();

            $obj = null;
            if($id > 0)
            {
                $obj = $class->getObjectUsingId($id);
                if($this->isCreateMode())
                    $obj->setId(0);
            }
            if(is_null($obj))
            {
                $obj = $class->getNewObject();
                $obj->setId(0);
            }

            $this->obj = $obj;
        }

        return $this->obj;
    }


    public function getForm()
    {
        if(is_null($this->form))
            $this->form = $this->getFormInstance();

        return $this->form;
    }

    public function getList()
    {
        if(is_null($this->list))
            $this->list = $this->getListInstance();
        
        return $this->list;
    }


    public function getExcludedFields()
    {
        return array($this->getDataClass()->getIdField()->getKeyname());
    }

    public function getExcludedFormFields()
    {
        return $this->getExcludedFields();
    }

    public function getIncludedFormFields()
    {
        return $this->getForm()->getDefaultIncludedFields();
    }

    public function getExcludedListFields()
    {
        return $this->getExcludedFields();
    }

    public function getIncludedListFields()
    {
        return $this->getList()->getDefaultIncludedFields();
    }


    public function getCustomListDisplayValueFields()
    {
        return array();
    }

    public function hasCustomListDisplayValue(MXT_AbstractDataField $field, $value)
    {
        return in_array($field->getKeyname(), $this->getCustomListDisplayValueFields());
    }

    public function getListDisplayValueUsingFieldAndValue(MXT_AbstractDataField $field, $value)
    {
        return $value;
    }


    public function getFilter()
    {
        $list = $this->getList();
        return $list->getFilter();
    }


    public function getFilterFields()
    {
        return array();
    }

    public function getSortFields()
    {
        return array();
    }


    public function getItemsPerPage()
    {
        return 50;
    }


    public function isListMode()
    {
        return (!$this->isEditMode()
            && !$this->isCreateMode()
            && $this->isReadAllowed());
    }

    public function isEditMode()
    {
        return ($this->getInput()->get('action') == 'edit'
            && $this->getId() >= 0
            && $this->isEditAllowed());
    }

    public function isCreateMode()
    {
        return ($this->getInput()->get('action') == 'create'
            && $this->isCreateAllowed());
    }


    public function post($args)
    {
        if($this->isEditMode() || $this->isCreateMode())
        {
            MXT_Language::pushGroup($this->getFormLanguageGroup());
            {
                // do the submit process
                $submit = $this->getForm()->submit();

                $this->get($args);
            }
            MXT_Language::popGroup();
        }
    }

    public function get($args)
    {
        if($this->isEditMode() || $this->isCreateMode())
        {
            MXT_Language::pushGroup($this->getFormLanguageGroup());
            {
                $this->getForm()->printFormAsHTML($this->getFormOutputter());
            }
            MXT_Language::popGroup();
        }
        else if($this->isListMode())
        {
            MXT_Language::pushGroup($this->getListLanguageGroup());
            {
                $this->outputFilterForm();

                $action = $this->getInput()->get('action');
                if($action == 'updated' || $action == 'created')
                    $this->outputSaveMessage($action, $this->getId());

                $this->getList()->outputUsingOutputter($this->getListOutputter());
            }
            MXT_Language::popGroup();
        }
    }


    public function outputFilterForm()
    {
        if($this->isFilterFormDisplayed())
        {
            $list = $this->getList();
            $filter = $list->getFilter();

            $filterForm = new MXT_DO_FilterForm($filter);
            $filterForm->printFormAsHTML($this->getFilterFormOutputter());
        }
    }

    public function isFilterFormDisplayed()
    {
        $list = $this->getList();
        $filter = $list->getFilter();

        return (count($filter->getFilterFields()) > 0);
    }


    public function getFormOutputter()
    {
        return new MXT_DescriptiveFormOutputter();
    }

    public function getListOutputter()
    {
        return new MXT_DO_ResponderListTableOutputter($this);
    }

    public function getFilterFormOutputter()
    {
        return new MXT_DO_DefaultFilterFormOutputter();
    }


    public function outputSaveMessage($type, $id)
    {
        $class = $this->getDataClass();

        ?>
         <div class="info-message">
          <?php echo 'The object was ' . $type . '.'; ?>
         </div>
        <?php
    }


    public function updateSucceeded(MXT_Event $event)
    {
        $this->saveSucceeded($event, 'updated');
    }

    public function updateFailed(MXT_Event $event)
    {
        $form = $this->getForm();
        $form->addError(new MXT_FormError($form, 0, 'There was an error during the update process.'));
    }

    public function createSucceeded(MXT_Event $event)
    {
        $this->saveSucceeded($event, 'created');
    }

    public function createFailed(MXT_Event $event)
    {
        $form = $this->getForm();
        $form->addError(new MXT_FormError($form, 0, 'There was an error during the create process.'));
    }

    public function saveSucceeded(MXT_Event $event, $action)
    {
        if($event instanceof MXT_DataObjectSaveEvent)
        {
            $class = $this->getDataClass();
            $idField = $class->getIdField();
            $obj = $event->getObject();
            if(!is_null($obj))
            {
                $filter = $this->getFilter();
                $filterFields = $filter->getFilterFields();
                $filterValues = array();
                foreach($filterFields as $keyname => $defaultValue)
                {
                    if($class->isField($keyname))
                    {
                        $value = $obj->getValue($keyname);
                        if(!is_null($value))
                            $filterValues[] = $keyname . '=' . $value->getId();
                    }
                }

                $filterQS = '';
                if(is_array($filterValues))
                    $filterQS = '&' . implode('&', $filterValues);

                $id = $obj->getId();
                HttpHeader::forwardTo($_SERVER['PHP_SELF'] . '?'
                    . $idField->getKeyname() . '=' . $id . '&action=' . $action . $filterQS);
            }
        }

        HttpHeader::forwardTo($_SERVER['PHP_SELF'] . '?action=' . $action);
    }
}



?>
