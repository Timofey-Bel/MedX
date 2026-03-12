<?php

namespace App\Http\Controllers;

class AjaxController extends Controller
{
    public function addToCart()
    {
        return response()->json(['status' => 'ok']);
    }

    public function removeFromCart()
    {
        return response()->json(['status' => 'ok']);
    }

    public function toggleFavorite()
    {
        return response()->json(['status' => 'ok']);
    }
}
