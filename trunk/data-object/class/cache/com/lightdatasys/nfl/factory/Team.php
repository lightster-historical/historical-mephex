<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'teamId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'location');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'mascot');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'conference');
$field->setAllowEmpty(false);
$field->setDefaultValue('AFC');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'division');
$field->setAllowEmpty(false);
$field->setDefaultValue('North');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'fontColor');
$field->setAllowEmpty(true);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'background');
$field->setAllowEmpty(true);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'borderColor');
$field->setAllowEmpty(true);
$field->setDefaultValue('');
$fields->replace($field);


    $this->idField = $fields->get('teamId');
}
?>