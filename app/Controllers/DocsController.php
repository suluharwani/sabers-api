<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class DocsController extends Controller
{
    public function index()
    {
        return view('docs/index');
    }
}