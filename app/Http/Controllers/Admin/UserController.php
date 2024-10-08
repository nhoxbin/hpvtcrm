<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\User\StoreUserRequest;
use App\Http\Requests\Admin\User\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index() {
        if (Auth::user()->is_super_admin) {
            $users = User::with(['roles', 'created_by_user'])->paginate();
            $roles = Role::all()->pluck('name');
        } else {
            if (Auth::user()->hasRole('OneBss Admin')) {
                $r = ['Sales', 'OneBss Sales',];
            } elseif (Auth::user()->hasPermissionTo('Write DigiShop')) {
                $r = ['Sales',];
            }
            $roles = Role::whereIn('name', $r)->pluck('name');
            $users = User::where('created_by_user_id', Auth::id())->with(['roles', 'created_by_user'])->paginate();
        }

        return Inertia::render('Admin/User/Index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function store(StoreUserRequest $request) {
        $user = User::create(array_merge($request->safe()->except(['role']), ['created_by_user_id' => Auth::id()]));
        $user->assignRole($request->safe()->only(['role']));
        return redirect()->route('admin.users.index')->with('msg', __('Thêm mới nhân viên thành công.'));
    }

    public function edit(User $user) {
        return response($user);
    }

    public function update(UpdateUserRequest $request, User $user) {
        if ($request->role == 'OneBss Sales') {
            if (!$user->hasRole('OneBss Admin')) {
                return back()->withSuccess('Bạn không có quyền OneBss để tạo thành viên!');
            }
        }
        $request->user()->fill($request->validated());
        $request->user()->save();

        $user->syncRoles($request->role);

        return back()->withSuccess('Cập nhật nhân viên thành công.');
    }

    public function destroy(Request $request, User $user) {
        $this->authorize('delete', $user);
        $user->delete();
        return response()->success('Xóa thành công.');
    }
}
