<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OneBssCustomer;
use Illuminate\Http\Request;
use Inertia\Inertia;

class OneBssCustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Inertia::render('Admin/OneBss/Customer/Index', [
            'customers' => OneBssCustomer::query()
                ->whereNotNull('goi_data')->with(['user'])
                ->when($request->search, function($query, $search) {
                    $query->where('phone', 'like', '%'.$search.'%');
                })->when($request->goi_data, function($query, $search) {
                    $query->whereJsonContains('goi_data', 'like', ['PACKAGE_NAME' => '%'.$search.'%']);
                })->when($request->expires_in, function($query, $search) {
                    $time = $search;
                    $query->whereJsonContains('goi_data', ['TIME_END' => '%'.$search.'%']);
                })->paginate()->withQueryString()
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
