<?php

namespace App\Http\Controllers;

use App\Helpers\Facades\OneSell;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        if (Auth::user()->hasRole('Super Admin')) {
            $customers = Customer::paginate();
        } else {
            $customers = Auth::user()->customers()->paginate();
        }
        return Inertia::render('Dashboard', compact('products', 'customers'));
    }
}
