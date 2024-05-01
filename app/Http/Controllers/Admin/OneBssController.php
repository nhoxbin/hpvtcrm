<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Facades\VNPTOneBss;
use App\Http\Controllers\Controller;
use App\Models\OneBssAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class OneBssController extends Controller
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
        return Inertia::render('Admin/OneBss/Login', [
            'status' => session('status')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function login(Request $request)
    {
        $is_login = true;
        $validated = $request->validated();
        $onebss = VNPTOneBss::login($validated);
        if ($onebss && $onebss['error_code'] == "BSS-00000000") {
            $data = $onebss['data'];
            if ($data['errorCode'] == 0) {
                $item = $data['item'];
                if (!empty($item) && $item['access_token']) {
                    $is_login = true;
                    OneBssAccount::updateOrCreate(['username' => $item['username']], ['password' => $validated['password'], 'access_token' => $item['access_token']]);
                }
            }
        }
        return Redirect::route('admin.onebss.create')->with('status', ($is_login ? 'Đăng nhập thành công!' : 'Đăng nhập không thành công!'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function get_oauth_token(Request $request)
    {
        $is_login = true;
        $validated = $request->validated();
        $digishop = VNPTOneBss::login($validated);
        if ($digishop['success'] && $digishop['statusCode'] == 200) {
            $data = $digishop['data'];
            if ($data['errorCode'] == 0) {
                $item = $data['item'];
                if (!empty($item) && $item['access_token']) {
                    $is_login = true;
                    OneBssAccount::updateOrCreate(['username' => $item['username']], ['password' => $validated['password'], 'access_token' => $item['access_token']]);
                }
            }
        }
        return Redirect::route('admin.digishop.create')->with('status', ($is_login ? 'Đăng nhập thành công!' : 'Đăng nhập không thành công!'));
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
