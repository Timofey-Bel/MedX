<?php include_once('site/classes/aModule.class.php');

class design_settings extends aModule
{
    function execute($arr)
    {
        print ($_SESSION['smarty']->fetch('medx/design_settings/design_settings.tpl'));
    }
}