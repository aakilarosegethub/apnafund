<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('admins')->orderBy('id')->get();

        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::orderBy('module')->orderBy('action')->get()->groupBy('module');
        $dashboardWidgets = config('dashboard_widgets', []);

        return view('admin.roles.create', compact('permissions', 'dashboardWidgets'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:roles,slug',
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request) {
            $role = Role::create([
                'name' => $request->name,
                'slug' => \Str::slug($request->slug),
                'description' => $request->description,
                'is_super_admin' => false,
                'is_protected' => false,
                'dashboard_widgets' => $request->input('dashboard_widgets', []),
            ]);
            $role->permissions()->sync($request->input('permissions', []));
        });

        $toast[] = ['success', 'Role created successfully.'];

        return redirect()->route('admin.roles.index')->withToasts($toast);
    }

    public function edit(Role $role)
    {
        if (! $role->canDelete() && $role->is_protected) {
            $toast[] = ['error', 'This role cannot be modified.'];

            return redirect()->route('admin.roles.index')->withToasts($toast);
        }
        $permissions = Permission::orderBy('module')->orderBy('action')->get()->groupBy('module');
        $dashboardWidgets = config('dashboard_widgets', []);
        $role->load('permissions');

        return view('admin.roles.edit', compact('role', 'permissions', 'dashboardWidgets'));
    }

    public function update(Request $request, Role $role)
    {
        if ($role->is_protected) {
            $toast[] = ['error', 'This role cannot be modified.'];

            return redirect()->route('admin.roles.index')->withToasts($toast);
        }
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:100|unique:roles,slug,'.$role->id,
            'description' => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $role) {
            $role->update([
                'name' => $request->name,
                'slug' => \Str::slug($request->slug),
                'description' => $request->description,
                'dashboard_widgets' => $request->input('dashboard_widgets', []),
            ]);
            if (! $role->is_super_admin) {
                $role->permissions()->sync($request->input('permissions', []));
            }
        });

        $toast[] = ['success', 'Role updated successfully.'];

        return redirect()->route('admin.roles.index')->withToasts($toast);
    }

    public function destroy(Role $role)
    {
        if (! $role->canDelete()) {
            $toast[] = ['error', 'This role cannot be deleted.'];

            return redirect()->route('admin.roles.index')->withToasts($toast);
        }
        if ($role->admins()->exists()) {
            $toast[] = ['error', 'Cannot delete role with assigned admins.'];

            return redirect()->route('admin.roles.index')->withToasts($toast);
        }

        $role->delete();
        $toast[] = ['success', 'Role deleted successfully.'];

        return redirect()->route('admin.roles.index')->withToasts($toast);
    }
}
