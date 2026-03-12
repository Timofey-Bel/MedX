<?php include_once('site/classes/aModule.class.php');

class main_tests extends aModule
{
    function execute($arr)
    {
        print ($_SESSION['smarty']->fetch('medx/main_tests/main_tests.tpl'));
    }
}