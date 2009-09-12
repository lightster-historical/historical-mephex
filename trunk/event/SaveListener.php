<?php


require_once PATH_LIB . 'com/mephex/event/Event.php';


interface MXT_SaveListener
{
    public function updateSucceeded(MXT_Event $event);
    public function updateFailed(MXT_Event $event);
    public function createSucceeded(MXT_Event $event);
    public function createFailed(MXT_Event $event);
}



?>
