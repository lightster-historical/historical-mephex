<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'seriesId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((10));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'keyname');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
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

$field = new MXT_DefaultDataField($this,'feedName');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);


    $this->idField = $fields->get('seriesId');
}
?>