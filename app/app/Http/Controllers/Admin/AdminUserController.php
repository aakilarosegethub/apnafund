<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminUserController extends Controller
{
    /**
     * Display a listing of sub admins.
     */
    public function index()
    {
        $admins = Admin::with('rbacRole')->orderBy('id', 'asc')->get();

        return view('admin.admin_user.index', compact('admins'));
    }

    /**
     * Show the form for creating a new sub admin.
     */
    public function create()
    {
        $roles = Role::where('is_super_admin', false)->orderBy('name')->get();

        return view('admin.admin_user.create', compact('roles'));
    }

    /**
     * Store a newly created sub admin.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:admins,username',
            'email' => 'required|email|unique:admins,email',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ], [
            'username.unique' => 'This username is already taken.',
            'email.unique' => 'This email is already registered.',
        ]);

        $roleId = $request->input('role_id');
        if ($roleId) {
            $role = Role::find($roleId);
            if ($role && $role->is_super_admin) {
                $roleId = null;
            }
        }

        Admin::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => 1,
            'role_id' => $roleId,
            'permissions' => null,
        ]);

        $toast[] = ['success', 'Sub admin created successfully.'];

        return redirect()->route('admin.admin-users.index')->withToasts($toast);
    }

    /**
     * Show the form for editing the specified sub admin.
     */
    public function edit(Admin $admin_user)
    {
        $roles = Role::where('is_super_admin', false)->orderBy('name')->get();

        return view('admin.admin_user.edit', compact('admin_user', 'roles'));
    }

    /**
     * Update the specified sub admin.
     */
    public function update(Request $request, Admin $admin_user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:admins,username,'.$admin_user->id,
            'email' => 'required|email|unique:admins,email,'.$admin_user->id,
            'password' => 'nullable|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        $admin_user->name = $request->name;
        $admin_user->username = $request->username;
        $admin_user->email = $request->email;
        if ($request->filled('password')) {
            $admin_user->password = Hash::make($request->password);
        }

        $roleId = $request->input('role_id');
        if ($roleId) {
            $role = Role::find($roleId);
            if ($role && $role->is_super_admin) {
                $roleId = null;
            }
        }
        $admin_user->role_id = $roleId;
        $admin_user->permissions = null;
        $admin_user->save();

        $toast[] = ['success', 'Sub admin updated successfully.'];

        return redirect()->route('admin.admin-users.index')->withToasts($toast);
    }

    /**
     * Remove the specified sub admin.
     */
    public function destroy(Admin $admin_user)
    {
        if ($admin_user->id === auth()->guard('admin')->id()) {
            $toast[] = ['error', 'You cannot delete yourself.'];

            return back()->withToasts($toast);
        }

        $admin_user->delete();

        $toast[] = ['success', 'Sub admin deleted successfully.'];

        return redirect()->route('admin.admin-users.index')->withToasts($toast);
    }

    /**
     * Toggle sub admin status.
     */
    public function status($id)
    {
        $admin = Admin::findOrFail($id);

        if ($admin->id === auth()->guard('admin')->id()) {
            $toast[] = ['error', 'You cannot change your own status.'];

            return back()->withToasts($toast);
        }

        $admin->status = $admin->status ? 0 : 1;
        $admin->save();

        $toast[] = ['success', 'Status updated successfully.'];

        return back()->withToasts($toast);
    }
}
