<?php

namespace App\Http\Controllers\Admin;

use Auth;
use App\User;
use App\Role;
use App\CreatedUser;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index() {
        $user = Auth::user();
        if ($user->isAdmin) {
            $users = User::all();
        } elseif ($user->isManager) {
            $users = $user->created_users;
        }
        return view('admin.user', compact('users'));
    }

    public function store(Request $request) {
        try {
            $user = new User;
            $user->name = $request->name;
            $user->username = $request->username;
            $user->password = bcrypt($request->password);
            $user->role_id = $request->user()->isAdmin ? $request->role : Role::where('name', 'sales')->pluck('id')[0];
            $user->save();

            $new_user = new CreatedUser;
            $new_user->user_id = $user->id;
            $new_user->by_id = $request->user()->id;
            $new_user->save();
        } catch (\Exception $e) {
            return back()->withError('Có lỗi khi tạo mới nhân viên! ' . $e->getMessage());
        }
        return back()->withSuccess('Thêm mới nhân viên thành công.');
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
