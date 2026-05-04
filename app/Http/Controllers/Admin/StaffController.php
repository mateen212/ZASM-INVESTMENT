<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Rules\FileTypeValidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class StaffController extends Controller
{
    public function index()
    {
        $pageTitle = 'All Staff';
        $allStaff = Admin::with('roles')->where('id', '!=', Auth::guard('admin')->id())
            ->whereHas('roles', function($q) {
                $q->where('name', '!=', 'partner');
            })->orderBy('id', 'desc')->paginate(getPaginate());
        return view('admin.staff.index', compact('pageTitle', 'allStaff'));
    }

    public function create()
    {
        $pageTitle = 'Add New Staff Member';
        
        // Get all roles except 'user' and organize them by organizational hierarchy
        $roles = Role::where('guard_name', 'admin')
                    ->where('name', '!=', 'Super Admin')
                    ->get();
        
        // Organize roles by category for better UI presentation
        $executiveRoles = $roles->filter(function($role) {
            // Explicitly include CTO in executive roles
            return in_array($role->name, ['CEO', 'CFO', 'COO', 'CTO', 'CLO']);
        });
        
        $managerRoles = $roles->filter(function($role) {
            // Explicitly include Technology Manager in manager roles
            return strpos($role->name, 'Manager') !== false && $role->name != 'Manager';
        });
        
        $otherRoles = $roles->filter(function($role) {
            return !in_array($role->name, ['CEO', 'CFO', 'COO', 'CTO', 'CLO']) && 
                   !(strpos($role->name, 'Manager') !== false && $role->name != 'Manager');
        });
        
        return view('admin.staff.create', compact('pageTitle', 'roles', 'executiveRoles', 'managerRoles', 'otherRoles'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50',
            'email' => 'required|email|max:90|unique:admins',
            'username' => 'required|max:50|unique:admins',
            'password' => 'required|confirmed|min:6',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpg', 'jpeg', 'png'])],
            'role' => 'required|exists:roles,id,guard_name,admin'
        ]);

        // Check if current user has permission to assign executive roles
        $role = Role::where('guard_name', 'admin')->find($request->role);
        $currentUser = auth()->guard('admin')->user();
        
        // Only Super Admin or CEO can assign executive roles
        if (in_array($role->name, ['CEO', 'CFO', 'COO', 'CTO', 'CLO']) &&
            !($currentUser->hasRole('Super Admin', 'admin') || $currentUser->hasRole('CEO', 'admin'))) {
            $notify[] = ['error', 'You do not have permission to assign executive roles'];
            return back()->withNotify($notify);
        }

        $staff = new Admin();
        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->username = $request->username;
        $staff->password = Hash::make($request->password);
        $staff->save();

        // Assign role with admin guard
        $staff->assignRole($role->name);

        if ($request->hasFile('image')) {
            try {
                $staff->image = uploadImage($request->image, imagePath()['profile']['admin']['path'], imagePath()['profile']['admin']['size'], $staff->image, imagePath()['profile']['admin']['thumb']);
                $staff->save();
            } catch (\Exception $exp) {
                $notify[] = ['error', 'Couldn\'t upload the image'];
                return back()->withNotify($notify);
            }
        }

        $notify[] = ['success', 'Staff added successfully'];
        return back()->withNotify($notify);
    }

    public function edit(Admin $staff)
    {
        $pageTitle = 'Edit Staff Member';
        
        // Get all roles except 'user' and organize them by organizational hierarchy
        $roles = Role::where('guard_name', 'admin')
                    ->where('name', '!=', 'Super Admin')
                    ->get();
        
        // Organize roles by category for better UI presentation
        $executiveRoles = $roles->filter(function($role) {
            return in_array($role->name, ['CEO', 'CFO', 'COO', 'CTO', 'CLO']);
        });
        
        $managerRoles = $roles->filter(function($role) {
            return strpos($role->name, 'Manager') !== false && $role->name != 'Manager';
        });
        
        $otherRoles = $roles->filter(function($role) {
            return !in_array($role->name, ['CEO', 'CFO', 'COO', 'CTO', 'CLO']) && 
                   !(strpos($role->name, 'Manager') !== false && $role->name != 'Manager');
        });
        
        return view('admin.staff.edit', compact('pageTitle', 'staff', 'roles', 'executiveRoles', 'managerRoles', 'otherRoles'));
    }

    public function update(Request $request, Admin $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins,email,'.$staff->id,
            'username' => 'required|alpha_num|min:6|unique:admins,username,'.$staff->id,
            'role' => 'required|exists:roles,id,guard_name,admin',
            'password' => ['nullable', 'confirmed', Password::min(6)]
        ]);

        // Check if current user has permission to assign executive roles
        $role = Role::where('guard_name', 'admin')->find($request->role);
        $currentUser = auth()->guard('admin')->user();
        
        // Only Super Admin or CEO can modify executive roles
        if (in_array($role->name, ['CEO', 'CFO', 'COO', 'CTO', 'CLO']) &&
            !($currentUser->hasRole('Super Admin', 'admin') || $currentUser->hasRole('CEO', 'admin'))) {
            $notify[] = ['error', 'You do not have permission to assign executive roles'];
            return back()->withNotify($notify);
        }

        // Prevent modification of Super Admin unless by another Super Admin
        if ($staff->hasRole('Super Admin', 'admin') && !$currentUser->hasRole('Super Admin', 'admin')) {
            $notify[] = ['error', 'You do not have permission to modify a Super Admin'];
            return back()->withNotify($notify);
        }

        $staff->name = $request->name;
        $staff->email = $request->email;
        $staff->username = $request->username;

        if ($request->password) {
            $staff->password = Hash::make($request->password);
        }

        $staff->save();

        // Update role with admin guard
        $staff->syncRoles([$role->name]);

        $notify[] = ['success', 'Staff updated successfully'];
        return back()->withNotify($notify);
    }

    public function status($id)
    {
        return Admin::changeStatus($id);
    }

    public function destroy(Admin $staff)
    {
        $staff->delete();

        $notify[] = ['success', 'Staff deleted successfully'];
        return back()->withNotify($notify);
    }

    public function roles()
    {
        $pageTitle = 'Roles & Permissions';
        
        // Organize roles by hierarchy
        $executiveRoles = Role::where('guard_name', 'admin')
                              ->whereIn('name', ['CEO', 'CFO', 'COO', 'CTO', 'CLO'])
                              ->get();
                              
        $managerRoles = Role::where('guard_name', 'admin')
                           ->where('name', 'like', '%Manager%')
                           ->whereNotIn('name', ['Manager'])
                           ->get();
                           
        $otherRoles = Role::where('guard_name', 'admin')
                         ->whereNotIn('name', ['user'])
                         ->whereNotIn('name', ['CEO', 'CFO', 'COO', 'CTO', 'CLO'])
                         ->where('name', 'not like', '%Manager%')
                         ->orWhere('name', 'Manager')
                         ->get();
        
        // Group permissions by category
        $permissions = Permission::where('guard_name', 'admin')
                               ->get()
                               ->groupBy(function($permission) {
                                   $parts = explode('.', $permission->name);
                                   return count($parts) > 1 ? $parts[0] : 'general';
                               });
        
        return view('admin.staff.roles', compact('pageTitle', 'executiveRoles', 'managerRoles', 'otherRoles', 'permissions'));
    }

    public function createRole()
    {
        $pageTitle = 'Create New Role';
        
        // Organize roles by hierarchy for the sidebar display
        $executiveRoles = Role::where('guard_name', 'admin')
                          ->whereIn('name', ['CEO', 'CFO', 'COO', 'CTO', 'CLO'])
                          ->get();
                          
        $managerRoles = Role::where('guard_name', 'admin')
                       ->where('name', 'like', '%Manager%')
                       ->whereNotIn('name', ['Manager'])
                       ->get();
                       
        $otherRoles = Role::where('guard_name', 'admin')
                     ->whereNotIn('name', ['user'])
                     ->whereNotIn('name', ['CEO', 'CFO', 'COO', 'CTO', 'CLO'])
                     ->where('name', 'not like', '%Manager%')
                     ->orWhere('name', 'Manager')
                     ->get();
        
        // Group permissions by category
        $permissions = Permission::where('guard_name', 'admin')
                           ->get()
                           ->groupBy(function($permission) {
                               $parts = explode('.', $permission->name);
                               return count($parts) > 1 ? $parts[0] : 'general';
                           });
                           
        return view('admin.staff.roles', compact('pageTitle', 'executiveRoles', 'managerRoles', 'otherRoles', 'permissions'));
    }

    public function storeRole(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:50|unique:roles,name,NULL,id,guard_name,admin',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'admin'
        ]);

        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        $notify[] = ['success', 'Role created successfully'];
        return back()->withNotify($notify);
    }

    public function editRole($id)
    {
        $role = Role::where('guard_name', 'admin')->findOrFail($id);
        
        // Prevent editing of Super Admin role
        if ($role->name === 'Super Admin') {
            $notify[] = ['error', 'Super Admin role cannot be edited'];
            return back()->withNotify($notify);
        }

        $pageTitle = 'Edit Role';
        
        // Get all permissions ordered by display_name
        $allPermissions = Permission::where('guard_name', 'admin')
                                   ->orderBy('display_name')
                                   ->get();
        
        // Group permissions by category in sidebar order
        $sidebarOrder = [
            'dashboard' => 0,
            'deals' => 1,
            'offerings' => 2,
            'investments' => 3,
            'assets' => 4,
            'property' => 5,
            'users' => 6,
            'staff' => 7,
            'roles' => 7,  // Same position as staff to group them together
            'deposits' => 8,
            'withdrawals' => 9,
            'documents' => 10,
            'emails' => 11,
            'updates' => 12,
            'support' => 13,
            'reports' => 14,
            'subscribers' => 16,
            'referrals' => 17,
            'settings' => 18,
            'system' => 19,
            'extra' => 19,  // Same position as system to match the sidebar
            'tech' => 21,
            'general' => 999,
        ];

        // Group permissions by category
        $permissions = $allPermissions->groupBy(function($permission) {
            // Special case for subscriber-related permissions
            if ($permission->name === 'subscriber' || strpos($permission->name, 'subscriber.') === 0) {
                return 'subscribers'; // Use plural form to match sidebar
            }
            
            // Group roles permissions with staff permissions
            if ($permission->name === 'roles' || strpos($permission->name, 'roles.') === 0) {
                return 'staff'; // Group roles with staff management
            }
            
            // Group system permissions with extra category
            if ($permission->name === 'system' || strpos($permission->name, 'system.') === 0) {
                return 'extra'; // Group system permissions under Extra category
            }
            
            // Handle other permissions
            $parts = explode('.', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'general';
        });

        // Sort permission groups by sidebar order
        $permissions = $permissions->sortBy(function($group, $key) use ($sidebarOrder) {
            return $sidebarOrder[$key] ?? 999;
        });
        
        return view('admin.staff.edit_role', compact('pageTitle', 'role', 'permissions'));
    }

    public function updateRole(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|string|max:255|unique:roles,name,'.$id.',id,guard_name,admin',
        ]);

        $role = Role::where('guard_name', 'admin')->findOrFail($id);
        
        // Prevent modification of executive role names
        if (in_array($role->name, ['Super Admin', 'CEO', 'CFO', 'COO', 'CTO', 'CLO'])) {
            if ($role->name != $request->name) {
                $notify[] = ['error', 'Executive role names cannot be modified'];
                return back()->withNotify($notify);
            }
            
            // For Super Admin, ensure it always has all permissions
            if ($role->name === 'Super Admin') {
                $role->name = $request->name;
                $role->save();
                
                $allPermissions = Permission::where('guard_name', 'admin')->get();
                $role->syncPermissions($allPermissions);
                
                $notify[] = ['success', 'Super Admin role updated with all permissions'];
                return back()->withNotify($notify);
            }
        }
        
        $role->name = $request->name;
        $role->save();

        // Validate that the permissions exist
        if ($request->has('permissions')) {
            $validPermissions = Permission::where('guard_name', 'admin')
                                        ->whereIn('name', $request->permissions)
                                        ->pluck('name')
                                        ->toArray();
            
            if (count($validPermissions) > 0) {
                $role->syncPermissions($validPermissions);
            } else {
                // Give at least dashboard.view permission
                $dashboardPerm = Permission::where('name', 'dashboard.view')
                                        ->where('guard_name', 'admin')
                                        ->first();
                if ($dashboardPerm) {
                    $role->syncPermissions([$dashboardPerm->name]);
                }
                
                $notify[] = ['warning', 'No valid permissions selected. Assigned default permission.'];
                return back()->withNotify($notify);
            }
        } else {
            // Give at least dashboard.view permission
            $dashboardPerm = Permission::where('name', 'dashboard.view')
                                    ->where('guard_name', 'admin')
                                    ->first();
            if ($dashboardPerm) {
                $role->syncPermissions([$dashboardPerm->name]);
            }
        }

        $notify[] = ['success', 'Role updated successfully'];
        return back()->withNotify($notify);
    }

    public function destroyRole($id)
    {
        $role = Role::where('guard_name', 'admin')->findOrFail($id);
        
        // Prevent deletion of protected roles
        if (in_array($role->name, ['Super Admin', 'CEO', 'CTO', 'Technology Manager', 'Staff'])) {
            $notify[] = ['error', 'This role cannot be deleted'];
            return back()->withNotify($notify);
        }
        
        // Check if any admin has this role using the model_has_roles table
        if ($role->users()->where('model_type', Admin::class)->count() > 0) {
            $notify[] = ['error', 'Role is assigned to staff members and cannot be deleted'];
            return back()->withNotify($notify);
        }

        $role->delete();

        $notify[] = ['success', 'Role deleted successfully'];
        return back()->withNotify($notify);
    }
}