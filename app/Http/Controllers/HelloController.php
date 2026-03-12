<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloController extends Controller
{
    public function index()
    {
        //
        $arr  = array(1,2,3);
        echo "<pre>"; print_r($arr); echo "</pre>"; //die();
        //        return view('home');
    }

}
