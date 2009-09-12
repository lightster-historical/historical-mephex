<?php


require_once PATH_LIB . 'com/mephex/data-object/DataField.php';
require_once PATH_LIB . 'com/mephex/util/context/DefaultContext.php';


// added: 2009/03/15 21:56
interface MXT_DataType
{
    public function getDefaultValue();
    public function allowNullValue();

    public function parseValue($value);

    public function getDisplayValue(MXT_DefaultContext $context, $value);

    public function encodeForDatabase($value);
    public function decodeEarlyFromDatabase($value);
    public function decodeFromDatabase($value);

    public function getFormField(MXT_DataField $field);
    public function encodeForForm($value);
    public function decodeFromForm($value);

    public function getCacheCode();
}



?>
