<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'weekId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_ForeignKeyDataField($this,'seasonId','season');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_DateDataField($this,'weekStart');
$field->setAllowEmpty(false);
$fields->replace($field);

$field = new MXT_DateDataField($this,'weekEnd');
$field->setAllowEmpty(false);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'winWeight');
$field->setAllowEmpty(false);
$field->setDefaultValue(1);
$fields->replace($field);

$field = new MXT_ForeignObjectDataField($this,'season','seasonId','LDS_FFB_SeasonClass');
$field->setAllowEmpty(false);
$field->setDefaultValue('');
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'gameCount');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);


    $this->idField = $fields->get('weekId');
}
?>