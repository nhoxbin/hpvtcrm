<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Facades\VNPTOneBss;
use App\Http\Controllers\Controller;
use App\Http\Requests\OneBss\LoginRequest;
use App\Http\Requests\OneBss\OAuthRequest;
use App\Models\OneBssAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            'status' => session('status'),
            'error' => session('error'),
            'secretCode' => session('secretCode'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function login(LoginRequest $request)
    {
        $validated = $request->validated();
        $onebss = VNPTOneBss::login($validated);
        if ($onebss['error_code'] == "BSS-00000000") {
            $data = $onebss['data'];
            $secretCode = $data['secretCode'];
            return Redirect::route('admin.onebss.create')->with('secretCode', $secretCode);
        }
        return Redirect::route('admin.onebss.create')->with('status', $onebss['message']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function oauth(OAuthRequest $request)
    {
        $is_login = false;
        $validated = $request->validated();
        $onebss = VNPTOneBss::oauth($validated);
        if (isset($onebss['access_token'])) {
            $is_login = true;
            OneBssAccount::updateOrCreate(['username' => $request->username], ['access_token' => $onebss['access_token'], 'expires_in' => $onebss['expires_in'], 'user_id' => Auth::id()]);
        }
        return Redirect::route('admin.onebss.create')->with(['status' => ($is_login ? 'Đăng nhập thành công!' : $onebss['message']), 'error' => !$is_login]);
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
