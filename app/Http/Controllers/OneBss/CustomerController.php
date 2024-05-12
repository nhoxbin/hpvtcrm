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
                ->when($request->goi_data, function($query, $search) {
                    $query->whereRaw('JSON_EXTRACT(`goi_data`, "$[*].PACKAGE_NAME") like "%'.$search.'%"');
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
        $account = OneBssAccount::getToken()->first();
        if ($account) {
            $balance = VNPTOneBss::getBalance($customer->phone, $account->access_token);
        }
    }
}
