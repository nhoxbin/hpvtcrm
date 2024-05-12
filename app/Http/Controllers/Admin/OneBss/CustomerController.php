<?php

namespace App\Http\Controllers\Admin\OneBss;

use App\Http\Controllers\Controller;
use App\Models\OneBssAccount;
use App\Models\OneBssCustomer;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
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
        $session = $request->user()->onebss_account()->whereNotNull('access_token')->first();
        return Inertia::render('Admin/OneBss/Customer/Index', [
            'users' => $request->user()->created_users,
            'customers' => OneBssCustomer::query()
                ->when($request->goi_data || $request->expires_in, function($query, $search) use ($request) {
                    if ($request->goi_data) {
                        $query->whereRaw('JSON_EXTRACT(`goi_data`, "$[*].PACKAGE_NAME") like "%'.$request->goi_data.'%"');
                    }
                    if ($request->expires_in) {
                        $query->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(JSON_EXTRACT(`goi_data`, "$[*].TIME_END"), "$[0]")) >= "'.Carbon::now()->format('d/m/Y H:i:s').'"');
                        $query->whereRaw('JSON_UNQUOTE(JSON_EXTRACT(JSON_EXTRACT(`goi_data`, "$[*].TIME_END"), "$[0]")) <= "'.Carbon::now()->addDays($request->expires_in)->format('d/m/Y H:i:s').'"');
                    }
                })
                ->with(['user'])
                ->orderBy('id', 'asc')
                ->paginate()
                ->withQueryString(),
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
            /* case 'duplicate':
                $customers = OneBssCustomer::selectRaw('MAX(id) as id, phone')->groupBy('phone')->havingRaw('COUNT(*) > 1')->pluck('phone', 'id');
                $ids = $customers->keys();
                $phones = $customers->values();
                OneBssCustomer::whereIn('phone', $phones)->whereNotIn('id', $ids)->delete();
                break; */
            /* case 'sales_state':
                $request->validate([
                    'sales_state' => 'required|in:'.implode(',', SalesStateEnum::values())
                ]);
                Customer::whereIn('sales_state', $request->command)->delete();
                break; */
        }
        return redirect()->route('admin.onebss.customers.index')->with('msg', 'Xóa dữ liệu thành công.');
    }
}
