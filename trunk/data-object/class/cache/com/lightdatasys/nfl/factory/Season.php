<?php
if(isset($this) && $this instanceof MXT_CacheableDataClass)
{
$field = new MXT_PrimaryKeyDataField($this,'seasonId');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);

$field = new MXT_IntegerDataField($this,'year');
$field->setAllowEmpty(false);
$field->setDefaultValue(0);
$fields->replace($field);


    $this->idField = $fields->get('seasonId');
}
?>