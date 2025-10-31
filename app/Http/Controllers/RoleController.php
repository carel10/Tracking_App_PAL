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
        return view('roles.form', compact('permissions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|unique:roles,name','label'=>'nullable','permissions'=>'nullable|array']);
        $role = Role::create($data);
        if (!empty($data['permissions'])) $role->permissions()->sync($data['permissions']);
        return redirect()->route('roles.index');
    }
}
