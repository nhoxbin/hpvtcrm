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
        $customers = Customer::query()->when(!Auth::user()->is_admin, function($query, $search) {
            $query->where('user_id', Auth::id());
        })->when($request->search, function($query, $search) {
            $query->where('phone', 'like', '%' . $search . '%');
        })->with('user')->paginate()->withQueryString();
        $sales_states = SalesStateEnum::trans();
        $search = $request->search;
        return Inertia::render('Dashboard', compact('customers', 'sales_states', 'search'));
    }
}
