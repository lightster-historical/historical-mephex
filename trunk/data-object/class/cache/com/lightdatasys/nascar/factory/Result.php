<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'resultId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((10));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'raceId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((5));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'driverId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((5));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'car');
$field->setAllowEmpty(false);
$field->setDefaultValue('0');
$field->setLength((5));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'start');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((3));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'finish');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((3));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'ledLaps');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((1));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'ledMostLaps');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((1));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'penalties');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((6));
$fields->replace($field);


    $this->idField = $fields->get('resultId');
}
?>