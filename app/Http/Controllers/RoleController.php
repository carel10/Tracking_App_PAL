<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Division;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class RoleController extends Controller
{
    /**
     * Display a listing of roles with users count.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $roles = Role::with(['permissions', 'users', 'division'])
            ->withCount('users')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Show the form for creating a new role.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $divisions = Division::all();
        
        return view('roles.form', compact('permissions', 'divisions'));
    }

    /**
     * Store a newly created role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:roles,name',
            'division_id' => 'nullable|exists:divisions,id',
            'hierarchy_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'division_id' => $data['division_id'] ?? null,
            'hierarchy_level' => $data['hierarchy_level'],
            'description' => $data['description'] ?? null,
        ]);

        // Assign permissions
        if (!empty($data['permission_ids'])) {
            $role->permissions()->sync($data['permission_ids']);
        }

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'role_created',
            'target_table' => 'roles',
            'target_id' => $role->id,
            'details' => [
                'role_name' => $role->name,
                'permission_count' => count($data['permission_ids'] ?? []),
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }

    /**
     * Show the form for editing the specified role.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\View\View
     */
    public function edit(Role $role)
    {
        $role->load('permissions');
        $permissions = Permission::orderBy('module')->orderBy('name')->get();
        $divisions = Division::all();
        
        return view('roles.form', compact('role', 'permissions', 'divisions'));
    }

    /**
     * Update the specified role in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Role  $role
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Role $role)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:roles,name,' . $role->id,
            'division_id' => 'nullable|exists:divisions,id',
            'hierarchy_level' => 'required|integer|min:0',
            'description' => 'nullable|string',
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->name = $data['name'];
        $role->division_id = $data['division_id'] ?? null;
        $role->hierarchy_level = $data['hierarchy_level'];
        $role->description = $data['description'] ?? null;
        $role->save();

        // Update permissions
        if ($request->has('permission_ids')) {
            $role->permissions()->sync($data['permission_ids'] ?? []);
        }

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'role_updated',
            'target_table' => 'roles',
            'target_id' => $role->id,
            'details' => [
                'role_name' => $role->name,
                'permission_count' => count($data['permission_ids'] ?? []),
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Role updated successfully.');
    }

    /**
     * View users belonging to a specific role.
     *
     * @param  \App\Models\Role  $role
     * @return \Illuminate\View\View
     */
    public function viewUsers(Role $role)
    {
        $role->load(['users.division']);
        $users = $role->users()->with('division')->paginate(20);
        
        return view('roles.users', compact('role', 'users'));
    }
}
