<?php


interface MXT_DO_AbstractEncoder
{
    public function encodeBoolean($value);
    public function encodeInteger($value);
    public function encodeFloat($value);
    public function encodeDateTime($value);
    public function encodeDate($value);
    public function encodeString($value);
}



?>
