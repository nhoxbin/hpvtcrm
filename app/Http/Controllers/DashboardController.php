<?php

namespace App\Http\Controllers;

use App\Enums\SalesStateEnum;
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
    public function index(Request $request)
    {
        if (Auth::user()->hasRole('Super Admin')) {
            $customers = new Customer;
        } else {
            $customers = Auth::user()->customers();
        }
        $customers = $customers->query()->when($request->search, function($query, $search) {
            $query->where('phone', 'like', '%' . $search . '%');
        })->with('user')->paginate()->withQueryString();
        $sales_states = SalesStateEnum::trans();
        $sessionMsg = session('msg');
        $search = $request->search;
        return Inertia::render('Dashboard', compact('customers', 'sales_states', 'sessionMsg', 'search'));
    }
}
