<?php

namespace App\Http\Controllers;

use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\SaleStage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    /* public function index()
    {
        if (Auth::user()->hasRole('Super Admin')) {
            $customers = Customer::paginate();
        } else {
            $customers = Auth::user()->customers()->paginate();
        }
        $sessionMsg = session('msg');
        return Inertia::render('Customer/Index', compact('customers', 'sessionMsg'));
    } */

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
        $customer->sales_state = $request->sales_state;
    	$customer->sales_note = $request->sales_note;
    	$customer->save();

        return back()->with('msg', 'Lưu thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Customer $customer)
    {
        //
    }
}
