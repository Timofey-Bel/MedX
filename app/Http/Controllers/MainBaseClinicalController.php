<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MainBaseClinicalController extends Controller
{
    public function index()
    {
        return view('main_base_clinical');
    }
}
