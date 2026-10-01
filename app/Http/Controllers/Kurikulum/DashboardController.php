<?php

namespace App\Http\Controllers\Kurikulum;

use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function index()
    {
        return view('kurikulum.dashboard');
    }
}
