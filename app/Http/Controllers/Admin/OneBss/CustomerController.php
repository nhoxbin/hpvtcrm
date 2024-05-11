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
            'customers' => OneBssCustomer::query()
                ->when($request->goi_data || $request->expires_in, function($query, $search) use ($request) {
                    if ($request->goi_data) {
                        $query->whereJsonContains('goi_data', ["PACKAGE_NAME" => $request->goi_data]);
                        // $query->whereRaw('JSON_EXTRACT(`goi_data`, "$[*].PACKAGE_NAME") like "%?%"', [$request->goi_data]);
                        // $query->where('goi_data->*->PACKAGE_NAME", "'.$request->goi_data.'")');
                        // $query->where('goi_data', 'like', '%'.$request->goi_data.'%');
                    }
                    if ($request->expires_in) {
                        // $query->whereRaw('JSON_EXTRACT(`goi_data` , "$[*].TIME_END") <= ?', [$request->expires_in]);
                    }
                })
                ->with(['user'])
                ->paginate()
                ->withQueryString(),
            'process_customers' => DB::select('call process_customers()')[0],
            'auth_status' => $session ? 'Phiên làm việc OneBss còn: ' . $session->updated_at->addSeconds($session->expires_in) : 'Phiên làm việc OneBss đã hết hiệu lực, Vui lòng đăng nhập!',
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
    public function update(Request $request, string $id)
    {
        DB::beginTransaction();
        try {
            if (in_array('all', $request->user_id)) {
                // chia đều tất cả user
                if ($request->user()->is_admin) {
                    $users = User::all();
                } else {
                    $users = $request->user()->created_users;
                }
            } else {
                // chọn nhiều user
                $users = User::whereIn('id', $request->user_id)->get();
            }

            $customers = OneBssCustomer::query()
                ->when($request->goi_data || $request->expires_in || $request->tra_sau, function($query, $search) use ($request) {
                    if ($request->goi_data) {
                        $query->whereJsonContains('goi_data', ["PACKAGE_NAME" => $request->goi_data]);
                        // $query->whereRaw('JSON_EXTRACT(`goi_data`, "$[*].PACKAGE_NAME") like "%?%"', [$request->goi_data]);
                        // $query->where('goi_data->*->PACKAGE_NAME", "'.$request->goi_data.'")');
                        // $query->where('goi_data', 'like', '%'.$request->goi_data.'%');
                    }
                    if ($request->expires_in) {
                        // $query->whereRaw('JSON_EXTRACT(`goi_data` , "$[*].TIME_END") <= ?', [$request->expires_in]);
                    }
                    if ($request->tra_sau) {
                        $query->where('tra_sau', $request->tra_sau);
                    }
                })
                ->whereNotNull('goi_data')->where('is_request', 1)->get();

            foreach ($customers as $customer) {
                // đoạn code phân cho 1 người
                if (is_numeric($request->user_id) && $request->user_id > 0) {
                    $user_id = $request->user_id;
                }
                $customers[] = [
                    'phone' => $phone,
                    'data' => $data,
                    'registered_at' => $registered_at,
                    'expired_at' => $expired_at,
                    'available_data' => json_encode($available_data),
                    'user_id' => $user_id ?? null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            OneBssCustomer::upsert();

            // đoạn code phân cho sales
            if (isset($users) && $users->count() > 0) {
                $customers = Customer::where('user_id', null)->get();
                $maxLength = intval($customers->count() / $users->count()) + 1;
                $pos = 0;
                foreach ($users as $key => $user) {
                    $usersAfter = $users->count() - $key - 1;

                    for ($i = 0; $i < $maxLength && $pos < $customers->count(); $i++) {
                        if ($usersAfter > 0) {
                            $elCountAfter = $customers->count() - $pos - 1;
                            $maxLengthAfter = floor(($elCountAfter / $usersAfter) + 1);
                            if ($i + 1 > $maxLengthAfter) {
                                break;
                            }
                        }
                        if (!empty($customers[$pos])) {
                            $customers[$pos]->user_id = $user->id;
                            $customers[$pos]->save();
                        }
                        $pos++;
                    }
                }
            }
            DB::commit();
            return redirect()->route('admin.customers.index')->with('msg', 'Đã tải dữ liệu khách hàng lên hệ thống.');
        } catch(\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return redirect()->route('admin.customers.index')->withError('Lỗi rồi!');
        }
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
