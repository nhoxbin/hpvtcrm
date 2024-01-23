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
        if (Auth::user()->is_admin) {
            $customers = Customer::paginate();
        } else {
            $customers = Auth::user()->customers()->paginate();
        }
        return Inertia::render('Customer/Index', compact('customers'));
    } */

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $customer->sales_state = $request->sales_state;
    	$customer->sales_note = $request->sales_note;
    	$customer->save();

        return redirect()->route('dashboard')->with('msg', 'Lưu thành công.');
    }
}
