<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Facades\VNPTDigiShop;
use App\Http\Controllers\Controller;
use App\Http\Requests\DigiShop\StoreUserRequest;
use App\Models\DigiShop;
use Illuminate\Http\Request;

class DigiShopController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();
        $digishop = VNPTDigiShop::login($validated);
        if ($digishop['success'] && $digishop['statusCode'] == 200) {
            $data = $digishop['data'];
            if ($data['errorCode'] == 0) {
                $item = $data['item'];
                if (!empty($item) && $item['access_token']) {
                    $username = $item['username'];
                    $access_token = $item['access_token'];
                    DigiShop::updateOrCreate(['username' => $username], ['access_token' => $access_token]);
                }
            }
        }
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
