<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'raceId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((5));
$fields->replace($field);

$field = new MXT_ForeignKeyDataField($this,'trackId','track');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_ForeignKeyDataField($this,'seasonId','season');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'raceNo');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((5));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'name');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((255));
$fields->replace($field);

$field = new MXT_DefaultDataField($this,'nascarComId');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$field->setLength((5));
$fields->replace($field);

$field = new MXT_ForeignKeyDataField($this,'stationId','station');
$field->setAllowEmpty(true);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'laps');
$field->setAllowEmpty(true);
$field->setDefaultValue(0);
$field->setLength((3));
$fields->replace($field);

$field = new MXT_DateTimeDataField($this,'date');
$field->setAllowEmpty(false);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'qualifyingRainedOut');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((1));
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'official');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$field->setLength((1));
$fields->replace($field);

$field = new MXT_BooleanDataField($this,'forPoints');
$field->setAllowEmpty(false);
$field->setDefaultValue(false);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'pickStatus');
$field->setAllowEmpty(true);
$field->setDefaultValue(0);
$field->setLength((1));
$fields->replace($field);

$field = new MXT_ModifiedTimeDataField($this,'lastUpdated');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_ForeignObjectDataField($this,'season','seasonId','LDS_SeasonClass');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_ForeignObjectDataField($this,'track','trackId','LDS_TrackClass');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_ForeignObjectDataField($this,'station','stationId','LDS_TvStationClass');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);


    $this->idField = $fields->get('raceId');
}
?>