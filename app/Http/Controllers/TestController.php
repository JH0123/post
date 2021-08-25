<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function index()
    {
        $name = '둘리';
        $age = '100000000';
        return view('test.show', compact('name', 'age'));
    }
}
