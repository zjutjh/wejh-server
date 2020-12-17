<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InsertName extends Controller
{
    public function insertName(Request $request) {
        $a = $request->a;
        $b = $request->b;
        echo $a + $b;
    }
}