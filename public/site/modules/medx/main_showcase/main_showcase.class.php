<?php include_once('site/classes/aModule.class.php');

class main_showcase extends aModule
{
    function execute($arr)
    {
        print ($_SESSION['smarty']->fetch('medx/main_showcase/main_showcase.tpl'));
    }
}