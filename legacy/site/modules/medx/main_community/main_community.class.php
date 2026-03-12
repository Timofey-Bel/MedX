<?php include_once('site/classes/aModule.class.php');

class main_community extends aModule
{
    function execute($arr)
    {
        print ($_SESSION['smarty']->fetch('medx/main_community/main_community.tpl'));
    }
}