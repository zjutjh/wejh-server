<?php

namespace App\Http\Controllers\Walk;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MainController extends Controller
{
    public function main(Request $request) {
        return view('walk');
    }
}
