<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Member;
use App\Models\User;
use App\Models\Admin;
use Spatie\Permission\Models\Role;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $pageTitle = 'Members';

        // Fetch only Admin users with the 'Partner' role
        $members = Admin::whereHas('roles', function ($query) {
            $query->where('name', 'Partner');
        })->paginate(10); // Adjust pagination as needed

        return view('admin.member.index', compact('pageTitle', 'members'));
    }

    public function edit(Admin $member)
    {
        // Ensure the member has the 'Partner' role
        if (!$member->hasRole('Partner')) {
            return redirect()->route('admin.member.index')->with('error', 'Member not found or not a Partner.');
        }

        $pageTitle = 'Edit Member';
        $roles = Role::all()->pluck('name', 'name'); // Get all roles for the dropdown

        return view('admin.member.edit', compact('pageTitle', 'member', 'roles'));
    }

    public function update(Request $request, Admin $member)
    {
        // Ensure the member has the 'Partner' role
        if (!$member->hasRole('Partner')) {
            return redirect()->route('admin.member.index')->with('error', 'Member not found or not a Partner.');
        }

        $request->validate([
            'role' => 'required|exists:roles,name',
        ]);

        // Sync the new role (replacing existing roles) or assign it
        $member->syncRoles([$request->role]);

        return redirect()->route('admin.member.index')->with('success', 'Member Role Updated Successfully');
    }

    public function destroy(Admin $member)
    {
        // Ensure the member has the 'Partner' role
        if (!$member->hasRole('Partner')) {
            return redirect()->route('admin.member.index')->with('error', 'Member not found or not a Partner.');
        }

        $member->delete();

        return redirect()->route('admin.member.index')->with('success', 'Member deleted successfully');
    }
}