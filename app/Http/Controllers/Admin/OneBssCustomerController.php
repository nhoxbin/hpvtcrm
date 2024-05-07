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
        dd(OneBssCustomer::query()
        ->whereNotNull('goi_data')->with(['user'])
        ->when($request->goi_data || $request->expires_in, function($query, $search) use ($request) {
            if ($request->goi_data) {
                // $query->whereJsonContains('goi_data', ["PACKAGE_NAME" => $request->goi_data]);
                $query->whereRaw('JSON_EXTRACT(`goi_data` , "$[*].PACKAGE_NAME") like "%?%"', [$request->goi_data]);
                // $query->where('goi_data->*->PACKAGE_NAME", "'.$request->goi_data.'")');
                // $query->where('goi_data', 'like', '%'.$request->goi_data.'%');
            }
            if ($request->expires_in) {
                // $query->whereRaw('JSON_EXTRACT(`goi_data` , "$[*].TIME_END") <= ?', [$request->expires_in]);
            }
        })->paginate());
        return Inertia::render('Admin/OneBss/Customer/Index', [
            'customers' => OneBssCustomer::query()
                ->whereNotNull('goi_data')->with(['user'])
                ->when($request->goi_data || $request->expires_in, function($query, $search) use ($request) {
                    if ($request->goi_data) {
                        // $query->whereJsonContains('goi_data', ["PACKAGE_NAME" => $request->goi_data]);
                        $query->whereRaw('JSON_EXTRACT(`goi_data` , "$[*].PACKAGE_NAME") like "%?%"', [$request->goi_data]);
                        // $query->where('goi_data->*->PACKAGE_NAME", "'.$request->goi_data.'")');
                        // $query->where('goi_data', 'like', '%'.$request->goi_data.'%');
                    }
                    if ($request->expires_in) {
                        // $query->whereRaw('JSON_EXTRACT(`goi_data` , "$[*].TIME_END") <= ?', [$request->expires_in]);
                    }
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
