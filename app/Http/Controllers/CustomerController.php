<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\SaleStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = Auth::user();
        $users = [];
        if ($user->isAdmin) {
            $users = User::all();
        } elseif ($user->isManager) {
            $users = $user->created_users;
        }
    	return view('customer.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Customer $customer)
    {
    	return view('customer.show', compact('customer'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Customer $customer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->sales_state_id = $request->sales_state;
    	$customer->description = $request->description;
    	if ($request->user()->hasRole('Super Admin')) {
	    	$customer->sales_admin_noted = $request->sales_admin_noted;
    	}
    	$customer->save();

    	return response()->success('Lưu thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
