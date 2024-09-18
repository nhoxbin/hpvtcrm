<?php

namespace App\Http\Controllers\Admin\DigiShop;

use App\Helpers\Facades\VNPTDigiShop;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\DigiShopAccount;
use App\Models\DigiShopCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Admin/DigiShop/Customer/Index', [
            'customers' => Auth::user()->digishop_customers()->paginate(10),
            'process_customers' => DB::select("call process_customers('digishop', {$request->user()->id})")[0],
            'msg' => session('msg'),
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
        $digishop = DigiShopAccount::where('status', true)->latest()->firstOrFail();
        $info = VNPTDigiShop::getInfo($validated['phone_number'], $digishop->access_token);
        if (!empty($info) && $info['success'] && $info['statusCode'] == 200) { //  && now() <= now()->createFromFormat('Y-m-d', '2024-05-13')
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
                DigiShopCustomer::updateOrCreate(['phone_number' => $validated['phone_number']], $insert);
                return response()->success('Success', $insert);
            }
        } else {
            Log::info('DigiShop/CustomerController');
            Log::info($info);
        }
        return response()->error('Cannot get info', 422);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
