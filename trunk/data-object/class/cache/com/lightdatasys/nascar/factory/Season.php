<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'seasonId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((3));
$fields->replace($field);

$field = new MXT_ForeignKeyDataField($this,'seriesId','series');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'year');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((6));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'relationTitle');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'maxPickCount');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((3));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'chaseRaceNo');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((3));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'chaseDriverCount');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((3));
$fields->replace($field);

$field = new MXT_ForeignObjectDataField($this,'series','seriesId','LDS_SeriesClass');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'raceCount');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);


    $this->idField = $fields->get('seasonId');
}
?>