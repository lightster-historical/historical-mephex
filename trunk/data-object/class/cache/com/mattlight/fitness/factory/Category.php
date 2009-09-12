<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'categoryId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'parentId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'title');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'repTypeId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);


    $this->idField = $fields->get('categoryId');
}
?>