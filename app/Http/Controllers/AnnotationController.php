<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class AnnotationController extends Controller
{
    public function test(): View
    {

        return view('annotation');
    }
}
