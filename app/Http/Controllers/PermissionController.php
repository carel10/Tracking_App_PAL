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
        return view('permissions.form');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|unique:permissions,name','label'=>'nullable']);
        Permission::create($data);
        return redirect()->route('permissions.index');
    }
}
