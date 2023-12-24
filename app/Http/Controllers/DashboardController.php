<?php

namespace App\Http\Controllers;

use App\Helpers\Facades\OneSell;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $categories = OneSell::categories('mobifone');
        $products = OneSell::products('mobifone', 51407);
        return Inertia::render('Dashboard', compact('products'));
    }
}
