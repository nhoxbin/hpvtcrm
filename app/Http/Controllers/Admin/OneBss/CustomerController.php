<?php

namespace App\Http\Controllers\Admin\OneBss;

use App\Http\Controllers\Controller;
use App\Models\OneBssAccount;
use App\Models\OneBssCustomer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if (Auth::user()->is_super_admin) {
            $session = OneBssAccount::getToken()->first();
        } else {
            $session = $request->user()->onebss_account()->getToken()->first();
        }

        $customers = new OneBssCustomer();
        return Inertia::render('Admin/OneBss/Customer/Index', [
            'users' => $request->user()->created_users()->role(['OneBss Admin', 'OneBss Sales'])->get(),
            'customers' => $customers->search($request)->paginate()->withQueryString(),
            'process_customers' => DB::select('call process_customers()')[0],
            'auth_status' => $session ? 'Phiên làm việc OneBss đến: ' . $session->updated_at->addSeconds($session->expires_in)->format('d/m/Y \l\ú\c H:i:s') : 'Phiên làm việc OneBss đã hết hiệu lực, Vui lòng đăng nhập!',
            'msg' => session('msg'),
            'error' => session('error'),
        ]);
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
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ?OneBssCustomer $customer)
    {

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, ?OneBssCustomer $customer)
    {
        if ($customer->exists) {
            $customer->delete();
            return response()->success('Xóa dữ liệu thành công.');
        }

        switch ($request->command) {
            case 'all':
                Schema::disableForeignKeyConstraints();
                OneBssCustomer::truncate();
                Schema::enableForeignKeyConstraints();
                break;
            case 'search':
                $customer = new OneBssCustomer();
                $customer->search($request)->delete();
                break;
            case 'sales_state':
                $customer = new OneBssCustomer();
                $customer->search($request)->whereNotNull('sales_state')->update(['user_id' => null, 'sales_state' => null, 'sales_note' => null, 'admin_note' => null]);
                // OneBssCustomer::whereNotNull('sales_state')->update(['sales_state' => null]);
                break;
            /* case 'sales_state':
                $request->validate([
                    'sales_state' => 'required|in:'.implode(',', SalesStateEnum::values())
                ]);
                OneBssCustomer::whereIn('sales_state', $request->command)->delete();
                break; */
        }
        return redirect()->route('admin.onebss.customers.index')->with('msg', 'Xóa dữ liệu thành công.');
    }
}
