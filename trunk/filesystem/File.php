<?php


class File
{
    public static function isChild($parent, $child)
    {
        $parent = realpath($parent);
        $child = realpath($child);

        if(substr($child, 0, strlen($parent)) == $parent)
            return true;

        return false;
    }
}


?>
