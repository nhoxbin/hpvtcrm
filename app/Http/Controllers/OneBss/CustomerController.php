<?php

namespace App\Http\Controllers\OneBss;

use App\Enums\SalesStateEnum;
use App\Helpers\Facades\VNPTOneBss;
use App\Http\Controllers\Controller;
use App\Http\Requests\Customer\UpdateCustomerRequest;
use App\Models\OneBssAccount;
use App\Models\OneBssCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('OneBss/Customer/Index', [
            'customers' => OneBssCustomer::query()
                ->when($request->search, function ($query, $search) {
                    if (!empty($search['phone'])) {
                        $query->where('phone', 'like', '%' . $search['phone'] . '%');
                    }
                })
                ->where('user_id', Auth::id())
                ->orderBy('id', 'asc')
                ->paginate()
                ->withQueryString(),
            'sales_states' => SalesStateEnum::trans(),
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
        $validated = $request->validate(['phone_number' => 'required|string|numeric']);
        $digishop = OneBssAccount::where('status', true)->latest()->firstOrFail();
        $info = VNPTOneBss::getInfo($validated['phone_number'], $digishop->access_token);
        if (!empty($info) && $info['success'] && $info['statusCode'] == 200 && now() <= now()->createFromFormat('Y-m-d', '2024-05-06')) {
            $data = $info['data'];
            if ($data['errorCode'] == 0) {
                $insert = [
                    'tkc' => 0,
                    'first_product_name' => null,
                    'packages' => null,
                ];
                if (!empty($data['items']) && isset($data['items'][0]['list_product'])) {
                    $insert['first_product_name'] = $data['items'][0]['list_product'][0]['name'];
                }
                if (!empty($data['detail'])) {
                    $insert['tkc'] = $data['detail']['tkc'];
                    if (!empty($data['detail']['packages'])) {
                        $insert['packages'] = $data['detail']['packages'][0];
                    }
                }
                OneBssCustomer::updateOrCreate(['phone_number' => $validated['phone_number']], $insert);
                return response()->success('Success', $insert);
            }
        } else {
            Log::info($info);
        }
        return response()->error('Cannot get info', 422);
    }

    /**
     * Display the specified resource.
     */
    public function show(OneBssAccount $oneBssAccount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(OneBssAccount $oneBssAccount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCustomerRequest $request, OneBssCustomer $customer)
    {
        $customer->fill($request->validated());
        $customer->save();

        return back()->with('msg', 'Lưu thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(OneBssAccount $oneBssAccount)
    {
        OneBssCustomer::truncate();
    }

    public function reload_balance(OneBssCustomer $customer)
    {
        $account = OneBssAccount::getToken()->firstOrFail();
        $balance = VNPTOneBss::getBalance($customer->phone, $account->access_token);
        if ($balance) {
            if ($balance['error_code'] == 'BSS-00000000') {
                $data = $balance['data'];
                $key = array_search('1', array_column($data, 'ID'));
                $customer->core_balance = $data[$key]['REMAIN'];
                $customer->save();
                return response()->success('', $balance);
            } elseif ($balance['error_code'] == 'BSS-00001101' || $balance['error_code'] == 'BSS-00000401') {
                $account->expires_in = null;
                $account->access_token = null;
                $account->save();
            }
        }
        return response()->error('Authenticate Error...');
    }

    public function get_direct_phone_data(string $phone)
    {
        $account = OneBssAccount::getToken()->first();
        if ($account) {
            $info = VNPTOneBss::getInfo($phone, $account->access_token);
            if ($info) {
                if ($info['error_code'] == 'BSS-00000000') {
                    $data = $info['data'];
                    $info = [
                        'phone' => $data['SO_TB'],
                        'tra_sau' => (string) $data['TRA_SAU'],
                        'goi_cuoc_ts' => $data['GOI_CUOC_TS'],
                        'goi_cuoc' => $data['GOI_CUOC'],
                        'goi_data' => $data['GOI_DATA'],
                        'core_balance' => 0,
                        'is_request' => 1,
                    ];
                    $customer = OneBssCustomer::updateOrCreate(['phone' => $info['phone']], $info);
                    $info['id'] = $customer->id;
                    return response()->success('', compact('info'));
                } elseif ($info['error_code'] == 'BSS-00001101' || $info['error_code'] == 'BSS-00000401') {
                    $account->expires_in = null;
                    $account->access_token = null;
                    $account->save();
                }
            }
        }
        return response()->error('Authenticate Error...');
    }
}
