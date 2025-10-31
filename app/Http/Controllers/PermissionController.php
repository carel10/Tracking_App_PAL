<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permission;

class PermissionController extends Controller
{
    public function index()
    {
        $permissions = Permission::paginate(50);
        return view('permissions.index', compact('permissions'));
    }

    public function create()
    {
        return view('permissions.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'permission_name' => 'required|unique:permissions,permission_name',
            'permission_code' => 'required|unique:permissions,permission_code',
            'category' => 'nullable'
        ]);
        
        Permission::create([
            'permission_name' => $data['permission_name'],
            'permission_code' => $data['permission_code'],
            'category' => $data['category'] ?? null,
            'created_at' => now()
        ]);
        
        return redirect()->route('permissions.index')
            ->with('success', 'Permission created successfully.');
    }
}
