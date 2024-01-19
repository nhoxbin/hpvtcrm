<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index() {
        return Inertia::render('Admin/User/Index', [
            'users' => User::with(['roles', 'created_by_user'])->withCount('customers')->paginate(),
            'roles' => Role::all()->pluck('name'),
            'sessionMsg' => session('msg'),
        ]);
    }

    public function store(StoreUserRequest $request) {
        $user = User::create($request->safe()->except(['role']) + ['created_by_user_id' => Auth::id()]);
        $user->assignRole($request->safe()->only(['role']));
        return redirect()->route('admin.users.index')->with('msg', __('Thêm mới nhân viên thành công.'));
    }

    public function edit(User $user) {
        return response($user);
    }

    public function update(Request $request, User $user) {
        $user->name = $request->name;
        $user->username = $request->username;
        if (!empty($request->password)) {
            $user->password = bcrypt($request->password);
        }
        $user->role_id = $request->user()->isAdmin ? $request->role : Role::where('name', 'sales')->pluck('id')[0];
        $user->save();

        return redirect()->back()->withSuccess('Cập nhật nhân viên thành công.');
    }

    public function destroy(User $user) {
        $user->delete();
        return response('Xóa nhân viên thành công.');
    }
}
