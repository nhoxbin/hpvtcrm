<?php

namespace App\Http\Controllers;

use App\Helpers\Facades\VNPTDigiShop;
use App\Models\DigiShopAccount;
use App\Models\DigiShopCustomer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class DigiShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Inertia::render('DigiShop/Index');
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
        $digishop = $request->user()->digishop_accounts()->where('status', true)->latest()->firstOrFail();
        $info = VNPTDigiShop::getInfo($validated['phone_number'], $digishop->access_token);
        if (!empty($info) && $info['success'] && $info['statusCode'] == 200) { //  && now() <= now()->createFromFormat('Y-m-d', '2024-05-13')
            $data = $info['data'];
            if ($data['errorCode'] == 0) {
                $insert = [
                    'tkc' => 0,
                    'first_product_name' => null,
                    'packages' => null,
                    'user_id' => $request->user()->id,
                    'is_request' => true,
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
        }
        Log::info('DigiShopController');
        Log::info($info);
        return response()->error('Cannot get info', 422);
    }

    /**
     * Display the specified resource.
     */
    public function show(DigiShop $digiShop)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DigiShop $digiShop)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DigiShop $digiShop)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DigiShopCustomer $digiShop)
    {
        Auth::user()->digishop_customers()->delete();
    }

    public function get_object(string $phone)
    {
        $account = DigiShopAccount::where(['status' => true, 'user_id' => Auth::user()->created_by_user_id ?? Auth::id()])->latest()->firstOrFail();
        $info = VNPTDigiShop::getInfo($phone, $account->access_token);
        if (!empty($info) && $info['success'] && $info['statusCode'] == 200) { //  && now() <= now()->createFromFormat('Y-m-d', '2024-05-13')
            $data = $info['data'];
            if ($data['errorCode'] == 0) {
                $integration = [];
                if (!empty($data['items']) && !empty($data['items'][1])) {
                    $integration = array_column($data['items'][1]['list_product'], 'name'); // tích hợp
                }
                return response()->success('Success', $integration);
            }
        }
        Log::info('DigiShopController');
        Log::info($info);
        return response()->error('Cannot get info', 422);
    }
}
