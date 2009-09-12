<?php


MXT_Language::pushLanguage('en-us');



MXT_Language::pushGroup('com.mephex.data-object');
{
    MXT_Language::pushGroup('.list');
    {
        MXT_Language::setStatement('submitFilter.title', 'Go');
        MXT_Language::setStatement('edit.title', 'Edit');
    }
    MXT_Language::popGroup();
}
MXT_Language::popGroup();



MXT_Language::popLanguage();


?>
