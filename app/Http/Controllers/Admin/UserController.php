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
            'users' => User::with(['roles', 'created_by_user'])->withCount('customers')->paginate()
        ]);
        /* $user = Auth::user();
        if ($user->isAdmin) {
            $users = User::all();
        } elseif ($user->isManager) {
            $users = $user->created_users;
        }
        return view('admin.user', compact('users')); */
    }

    public function store(StoreUserRequest $request) {
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'created_by_user_id' => Auth::id(),
        ]);
        $user->assignRole($request->role);
        return response('Thêm mới nhân viên thành công.');
    }

    public function edit(User $user) {
        /* return Inertia::render('Tweets/Show')
            ->with([
                'user' => $user,
                // 'tweet' => $tweet,
            ])
            ->baseRoute('users.index', $user); */
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
