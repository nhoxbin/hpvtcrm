<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use App\Http\Controllers\Controller;

class CustomerController extends Controller
{
    public function update() {
        
    }

    public function destroy(Customer $customer) {
        $customer->delete();
        return response('Xóa khách hàng thành công.');
    }
}
