<?php include_once('site/classes/aModule.class.php');

class faq extends aModule
{
    function execute($arr)
    {
        print ($_SESSION['smarty']->fetch('medx/faq/faq.tpl'));
    }
}