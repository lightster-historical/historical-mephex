<?php


interface MXT_DO_AbstractDecoder
{
    public function decodeBoolean($value);
    public function decodeInteger($value);
    public function decodeFloat($value);
    public function decodeDateTime($value);
    public function decodeDate($value);
    public function decodeString($value);
}



?>
