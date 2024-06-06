<?php

namespace App\Http\Controllers\Admin\OneBss;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (Auth::user()->is_super_admin) {
            $users = User::role(['OneBss Sales'])->with(['roles', 'created_by_user'])->withCount('onebss_customers')->paginate();
        } else {
            $users = Auth::user()->created_users()->role(['OneBss Sales'])->with(['roles', 'created_by_user'])->withCount('onebss_customers')->paginate();
        }

        return Inertia::render('Admin/OneBss/User/Index', [
            'users' => $users,
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
    public function destroy(Request $request, User $user) {
        $this->authorize('delete', $user);
        $user->onebss_customers()->update(['user_id' => null, 'sales_state' => null, 'sales_note' => null, 'admin_note' => null]);
        return response()->success('Xóa thành công.');
    }
}
