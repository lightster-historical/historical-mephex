<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'driverId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((5));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'firstName');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'lastName');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'color');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'background');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'border');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);


    $this->idField = $fields->get('driverId');
}
?>