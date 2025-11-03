<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\User;
use App\Models\Role;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class DivisionController extends Controller
{
    /**
     * Display a listing of divisions.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $divisions = Division::withCount(['users', 'roles'])
            ->orderBy('name')
            ->get();
        
        return view('divisions.index', compact('divisions'));
    }

    /**
     * Show the form for creating a new division.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('divisions.form');
    }

    /**
     * Store a newly created division in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:divisions,name',
            'description' => 'nullable|string',
        ]);

        $division = Division::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'division_created',
            'target_table' => 'divisions',
            'target_id' => $division->id,
            'details' => [
                'division_name' => $division->name,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('divisions.index')
            ->with('success', 'Division created successfully.');
    }

    /**
     * Show the form for editing the specified division.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\View\View
     */
    public function edit(Division $division)
    {
        return view('divisions.form', compact('division'));
    }

    /**
     * Update the specified division in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Division  $division
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Division $division)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:divisions,name,' . $division->id,
            'description' => 'nullable|string',
        ]);

        $division->name = $data['name'];
        $division->description = $data['description'] ?? null;
        $division->save();

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'division_updated',
            'target_table' => 'divisions',
            'target_id' => $division->id,
            'details' => [
                'division_name' => $division->name,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('divisions.index')
            ->with('success', 'Division updated successfully.');
    }

    /**
     * View users belonging to a specific division.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\View\View
     */
    public function viewUsers(Division $division)
    {
        $division->load('users.roles');
        $users = $division->users()->with('roles')->paginate(20);
        
        return view('divisions.users', compact('division', 'users'));
    }

    /**
     * View roles belonging to a specific division.
     *
     * @param  \App\Models\Division  $division
     * @return \Illuminate\View\View
     */
    public function viewRoles(Division $division)
    {
        $division->load('roles.permissions', 'roles.users');
        $roles = $division->roles()->with(['permissions', 'users'])->withCount('users')->paginate(20);
        
        return view('divisions.roles', compact('division', 'roles'));
    }
}

