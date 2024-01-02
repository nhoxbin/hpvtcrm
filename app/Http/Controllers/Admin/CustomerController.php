<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CustomerController extends Controller
{
    public function index() {
        return Inertia::render('Admin/Customer/Index', [
            'customers' => Customer::with(['user', 'sales_state'])->paginate()
        ]);
    }

    public function update() {
        
    }

    public function destroy(Customer $customer) {
        $customer->delete();
        return response('Xóa khách hàng thành công.');
    }
}
