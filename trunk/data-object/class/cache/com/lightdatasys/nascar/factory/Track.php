<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'trackId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((3));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'name');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'shortName');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'location');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);

$field = new MXT_FloatDataField($this,'length');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);


    $this->idField = $fields->get('trackId');
}
?>