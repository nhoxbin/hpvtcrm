<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Customer;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function update() {
        
    }

    public function destroy(Customer $customer) {
        $customer->delete();
        return response('Xóa khách hàng thành công.');
    }
}
