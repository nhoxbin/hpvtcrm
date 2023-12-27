<?php

namespace App\Http\Controllers;

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
        $sales_stages = SaleStage::all();
    	return view('customer.index', compact('users', 'sales_stages'));
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
        $sales_stages = SaleStage::all();
    	return view('customer.show', compact('customer', 'sales_stages'));
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
    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'sales_stage' => 'exists:sales_stages,id'
        ]);
        
        $customer->sales_stage_id = $request->sales_stage;
    	$customer->description = $request->description;
    	if (Auth::user()->role) {
	    	$customer->sales_admin_noted = $request->sales_admin_noted;
    	}
    	$customer->save();

    	return redirect()->back()->withSuccess('Lưu thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
