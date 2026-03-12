<?php include_once('site/classes/aModule.class.php');

class support_settings extends aModule
{
    function execute($arr)
    {
        print ($_SESSION['smarty']->fetch('medx/support_settings/support_settings.tpl'));
    }
}