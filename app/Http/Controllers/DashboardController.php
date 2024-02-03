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
        $validated = $request->validate([
            'search' => 'nullable|array',
        ]);
        $customers = Customer::query()->when(!Auth::user()->is_admin, function($query) {
            $query->where('user_id', Auth::id());
        })->when($request->search, function($query, $search) {
            $query->where('phone', 'like', '%' . $search['customers'] . '%');
        })->with(['user'])->paginate()->withQueryString();
        $sales_states = SalesStateEnum::trans();
        return Inertia::render('Dashboard', compact('customers', 'sales_states'));
    }
}
