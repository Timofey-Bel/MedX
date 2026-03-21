<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainBaseController extends Controller
{
    public function index()
    {
        return view('main_base');
    }
}
