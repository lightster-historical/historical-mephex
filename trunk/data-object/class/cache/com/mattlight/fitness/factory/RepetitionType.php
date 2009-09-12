<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'repTypeId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'fullSingular');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'fullPlural');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'abbrSingular');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'abbrPlural');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);


    $this->idField = $fields->get('repTypeId');
}
?>