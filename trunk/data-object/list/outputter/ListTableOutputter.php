<?php


require_once PATH_LIB . 'com/mephex/language/Language.php';
require_once PATH_LIB . 'com/mephex/data-object/list/outputter/AbstractListOutputter.php';


class MXT_DO_ListTableOutputter implements MXT_DO_AbstractListOutputter
{
    public function outputList(MXT_DO_AbstractList $list)
    {
        MXT_Language::loadFile('com/mephex/data-object');

        $filter = $list->getFilter();
        $pageRange = $filter->getPageRange();

        if($pageRange->getPageCount() > 1)
            $pageRange->outputDefault();
        ?>
         <div class="<?php echo $this->getDivStyleClass(); ?>">
          <table>
        <?php
        echo $this->outputTableHeader($list);
        ?>
           <tbody>
        <?php
        $list->outputItemsUsingOutputter($this);
        ?>
           </tbody>
          </table>
         </div>
         <br class="clear" />
        <?php
        if($pageRange->getPageCount() > 1)
            $pageRange->outputDefault();
    }

    public function getDivStyleClass()
    {
        return 'table-default';
    }


    public function outputListObject(MXT_DO_AbstractList $list, $i)
    {
        if($i % 2 == 0)
            $styleClass = 'row-a';
        else
            $styleClass = 'row-b';
        ?>
         <tr class="<?php echo $styleClass; ?>">
          <?php $this->outputListObjectRow($list, $i); ?>
         </tr>
        <?php
    }


    public function outputTableHeader(MXT_DO_AbstractList $list)
    {
        ?>
          <thead>
           <tr>
            <?php $this->outputTableHeaderRow($list); ?>
           </tr>
          </thead>
        <?php
    }

    public function outputTableHeaderRow(MXT_DO_AbstractList $list)
    {
        $fields = $list->getFields();
        foreach($fields as $keyname => $field)
        {
            echo '<th>' , MXT_Language::getStatement("$keyname.title") , '</th>';
        }

        $this->outputTableHeaderRowExtras($list);
    }

    public function outputTableHeaderRowExtras(MXT_DO_AbstractList $list)
    {
    }


    public function outputListObjectRow(MXT_DO_AbstractList $list, $i)
    {
        $obj = $list->getObjectUsingOffset($i);
        $fields = $list->getFields();
        foreach($fields as $keyname => $field)
        {
            echo '<td>';
            echo $list->getDisplayValueUsingFieldAndObject($field, $obj);;
            echo '</td>';
        }

        $this->outputListObjectRowExtras($list, $obj);
    }

    public function outputListObjectRowExtras(MXT_DO_AbstractList $list, MXT_DataObject $obj)
    {
    }
}



?>
