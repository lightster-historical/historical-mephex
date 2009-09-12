<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'gameId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_ForeignKeyDataField($this,'weekId','week');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_ForeignKeyDataField($this,'awayId','awayTeam');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_ForeignKeyDataField($this,'homeId','homeTeam');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'awayScore');
$field->setAllowEmpty(true);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'homeScore');
$field->setAllowEmpty(true);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_DateTimeDataField($this,'gameTime');
$field->setAllowEmpty(false);
$fields->replace($field);

$field = new MXT_ForeignObjectDataField($this,'week','weekId','LDS_FFB_WeekClass');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_ForeignObjectDataField($this,'awayTeam','awayId','LDS_FFB_TeamClass');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_ForeignObjectDataField($this,'homeTeam','homeId','LDS_FFB_TeamClass');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);


    $this->idField = $fields->get('gameId');
}
?>