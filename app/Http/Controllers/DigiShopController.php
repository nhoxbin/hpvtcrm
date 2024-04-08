<?php

namespace App\Http\Controllers;

use App\Helpers\Facades\VNPTDigiShop;
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
    public function store(Request $request)
    {
        $validated = $request->validate(['phone_number' => 'required|string|numeric']);
        $digishop = DigiShop::latest();
        VNPTDigiShop::getInfo($validated['phone_number'], $digishop->access_token);
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
    public function destroy(DigiShop $digiShop)
    {
        //
    }
}
