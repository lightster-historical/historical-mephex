<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'eventId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'categoryId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_DateTimeDataField($this,'eventDate');
$field->setAllowEmpty(false);
$fields->replace($field);

$field = new MXT_FloatDataField($this,'repetitions');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'timeElapsed');
$field->setAllowEmpty(true);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'notes');
$field->setAllowEmpty(true);
$field->setDefaultValue('');
$fields->replace($field);


    $this->idField = $fields->get('eventId');
}
?>