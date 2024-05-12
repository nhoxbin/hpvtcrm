<?php

namespace App\Http\Controllers\Admin\OneBss;

use App\Helpers\Facades\VNPTOneBss;
use App\Http\Controllers\Controller;
use App\Http\Requests\OneBss\LoginRequest;
use App\Http\Requests\OneBss\OAuthRequest;
use App\Jobs\OneBssClearAuth;
use App\Models\OneBssAccount;
use App\Models\OneBssCustomer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class OAuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
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
        if ($onebss) {
            if ($onebss['error_code'] == "BSS-00000000") {
                $data = $onebss['data'];
                $secretCode = $data['secretCode'];
                return Redirect::route('admin.onebss.create')->with('secretCode', $secretCode);
            }
            return Redirect::route('admin.onebss.create')->with('status', $onebss['message']);
        }
        return Redirect::route('admin.onebss.create')->with('status', 'Something happen! Please try again.');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function oauth(OAuthRequest $request)
    {
        $validated = $request->validated();
        $onebss = VNPTOneBss::oauth($validated);
        if (isset($onebss['access_token'])) {
            $account = OneBssAccount::updateOrCreate(['username' => $request->username], ['access_token' => $onebss['access_token'], 'expires_in' => $onebss['expires_in'], 'user_id' => Auth::id()]);
            Log::info($onebss);
            OneBssClearAuth::dispatch($account)->delay(Carbon::now()->addSeconds($onebss['expires_in']));
            return Redirect::route('admin.onebss.create')->with(['status' => 'Đăng nhập thành công!', 'error' => false]);
        }
        return Redirect::route('admin.onebss.create')->with(['status' => $onebss ? $onebss['message'] : 'Something happen! Please try again.', 'error' => true]);
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