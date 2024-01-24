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
        $customers = Customer::query()->when(!Auth::user()->is_admin, function($query) {
            $query->where('user_id', Auth::id());
        })->when(!empty($request->search['customers']), function($query, $search) use ($request) {
            $query->where('phone', 'like', '%' . $request->search['customers'] . '%');
        })->with('user')->paginate()->withQueryString();
        $sales_states = SalesStateEnum::trans();
        return Inertia::render('Dashboard', compact('customers', 'sales_states'));
    }
}
