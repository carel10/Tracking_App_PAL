<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->paginate(20);
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all();
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'role_name' => 'required|unique:roles,role_name',
            'role_description' => 'nullable',
            'permissions' => 'nullable|array'
        ]);
        
        $role = Role::create([
            'role_name' => $data['role_name'],
            'role_description' => $data['role_description'] ?? null,
            'created_at' => now()
        ]);
        
        if (!empty($data['permissions'])) {
            $role->permissions()->sync($data['permissions']);
        }
        
        return redirect()->route('roles.index')
            ->with('success', 'Role created successfully.');
    }
}
