<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserManagementController extends Controller
{
    public function index()
    {
        $pageTitle = 'User Management';
        $users = User::paginate(getPaginate());
        $roles = Role::all();
        return view('admin.users.management.index', compact('pageTitle', 'users', 'roles'));
    }

    public function updateRoles(Request $request, User $user)
    {
        $request->validate([
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,name'
        ]);

        $user->syncRoles($request->roles);

        $notify[] = ['success', 'User roles updated successfully'];
        return back()->withNotify($notify);
    }

    public function verify(User $user)
    {
        $user->status = 1;
        $user->save();

        $notify[] = ['success', 'User verified successfully'];
        return back()->withNotify($notify);
    }
}
