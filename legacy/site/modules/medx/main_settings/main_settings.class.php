<?php include_once('site/classes/aModule.class.php');

class main_settings extends aModule
{
    function execute($arr)
    {
        print ($_SESSION['smarty']->fetch('medx/main_settings/main_settings.tpl'));
    }
}