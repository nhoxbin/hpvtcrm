<?php

namespace App\Http\Controllers\Admin\DigiShop;

use App\Helpers\Facades\VNPTDigiShop;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DigiShop\StoreUserRequest;
use App\Models\DigiShopAccount;
use App\Models\OneBssAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class AccountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request) {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/DigiShop/Login', [
            'status' => session('status')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request)
    {
        $is_login = true;
        $validated = $request->validated();
        $digishop = VNPTDigiShop::login($validated);
        if ($digishop['success'] && $digishop['statusCode'] == 200) {
            $data = $digishop['data'];
            if ($data['errorCode'] == 0) {
                $item = $data['item'];
                if (!empty($item) && $item['access_token']) {
                    $is_login = true;
                    DigiShopAccount::updateOrCreate(['username' => $item['username']], ['password' => $validated['password'], 'access_token' => $item['access_token'], 'user_id' => Auth::id()]);
                }
            }
        }
        return Redirect::route('admin.digishop.accounts.create')->with('status', ($is_login ? 'Đăng nhập thành công!' : 'Đăng nhập không thành công!'));
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
    public function destroy(OneBssAccount $account)
    {
        $this->authorize('delete', $account);
        $account->delete();
        return response()->success('Xóa tài khoản OneBss thành công.');
    }
}
