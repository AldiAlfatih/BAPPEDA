<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;
class Triwulan1Controller extends Controller
{
    public function index()
    {
        return Inertia::render('Triwulan1');
    }
}
