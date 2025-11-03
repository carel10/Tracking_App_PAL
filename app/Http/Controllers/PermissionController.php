<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * Display a listing of permissions with search and filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = Permission::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('module', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by module
        if ($request->filled('module')) {
            $query->where('module', $request->module);
        }

        // Get unique modules for filter dropdown
        $modules = Permission::distinct()->pluck('module')->filter()->sort()->values();

        // Get all permissions grouped by module for display
        $permissions = $query->orderBy('module')->orderBy('name')->get();
        $permissionsPaginated = $query->orderBy('module')->orderBy('name')->paginate(50)->withQueryString();

        return view('permissions.index', compact('permissionsPaginated', 'permissions', 'modules'));
    }

    /**
     * Show the form for creating a new permission.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        // Get unique modules for dropdown
        $existingModules = Permission::distinct()->pluck('module')->filter()->sort()->values();
        
        return view('permissions.form', compact('existingModules'));
    }

    /**
     * Store a newly created permission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:permissions,name',
            'module' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $permission = Permission::create([
            'name' => $data['name'],
            'module' => $data['module'],
            'description' => $data['description'] ?? null,
        ]);

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'permission_created',
            'target_table' => 'permissions',
            'target_id' => $permission->id,
            'details' => [
                'permission_name' => $permission->name,
                'module' => $permission->module,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }

    /**
     * Show the form for editing the specified permission.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\View\View
     */
    public function edit(Permission $permission)
    {
        $existingModules = Permission::distinct()->pluck('module')->filter()->sort()->values();
        
        return view('permissions.form', compact('permission', 'existingModules'));
    }

    /**
     * Update the specified permission in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Permission $permission)
    {
        $data = $request->validate([
            'name' => 'required|string|max:150|unique:permissions,name,' . $permission->id,
            'module' => 'required|string|max:100',
            'description' => 'nullable|string',
        ]);

        $permission->name = $data['name'];
        $permission->module = $data['module'];
        $permission->description = $data['description'] ?? null;
        $permission->save();

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'permission_updated',
            'target_table' => 'permissions',
            'target_id' => $permission->id,
            'details' => [
                'permission_name' => $permission->name,
                'module' => $permission->module,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission updated successfully.');
    }

    /**
     * Remove the specified permission from storage.
     *
     * @param  \App\Models\Permission  $permission
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Permission $permission)
    {
        $permissionName = $permission->name;
        
        // Check if permission is assigned to any roles
        if ($permission->roles()->count() > 0) {
            return redirect()->route('permissions.index')
                ->with('error', 'Cannot delete permission that is assigned to roles.');
        }

        $permission->delete();

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'permission_deleted',
            'target_table' => 'permissions',
            'target_id' => $permission->id,
            'details' => [
                'permission_name' => $permissionName,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('permissions.index')
            ->with('success', 'Permission deleted successfully.');
    }
}
