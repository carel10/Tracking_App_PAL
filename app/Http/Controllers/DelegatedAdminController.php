<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminScope;
use App\Models\User;
use App\Models\Division;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class DelegatedAdminController extends Controller
{
    /**
     * Display a listing of delegated admins.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $adminScopes = AdminScope::with(['adminUser', 'division'])
            ->orderBy('id', 'desc')
            ->paginate(20);
        
        $users = User::where('status', 'active')
            ->orderBy('full_name')
            ->get();
        
        $divisions = Division::orderBy('name')->get();
        
        return view('delegated_admins.index', compact('adminScopes', 'users', 'divisions'));
    }

    /**
     * Store a newly assigned admin to division.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'admin_user_id' => 'required|exists:users,id',
            'division_id' => 'required|exists:divisions,id',
            'can_manage_users' => 'nullable|boolean',
            'can_manage_roles' => 'nullable|boolean',
        ]);

        // Check if this admin is already assigned to this division
        $existing = AdminScope::where('admin_user_id', $data['admin_user_id'])
            ->where('division_id', $data['division_id'])
            ->first();

        if ($existing) {
            return redirect()->route('delegated-admins.index')
                ->with('error', 'This admin is already assigned to this division.');
        }

        $adminScope = AdminScope::create([
            'admin_user_id' => $data['admin_user_id'],
            'division_id' => $data['division_id'],
            'can_manage_users' => $data['can_manage_users'] ?? false,
            'can_manage_roles' => $data['can_manage_roles'] ?? false,
        ]);

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'delegated_admin_assigned',
            'target_table' => 'admin_scopes',
            'target_id' => $adminScope->id,
            'details' => [
                'admin_user_id' => $adminScope->admin_user_id,
                'division_id' => $adminScope->division_id,
                'can_manage_users' => $adminScope->can_manage_users,
                'can_manage_roles' => $adminScope->can_manage_roles,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('delegated-admins.index')
            ->with('success', 'Admin assigned to division successfully.');
    }

    /**
     * Update permission for a delegated admin.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $adminScope
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePermission(Request $request, $adminScope)
    {
        $data = $request->validate([
            'permission_type' => 'required|in:can_manage_users,can_manage_roles',
            'value' => 'required|boolean',
        ]);

        $adminScopeModel = AdminScope::findOrFail($adminScope);
        $adminScopeModel->{$data['permission_type']} = $data['value'];
        $adminScopeModel->save();

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'delegated_admin_permission_updated',
            'target_table' => 'admin_scopes',
            'target_id' => $adminScopeModel->id,
            'details' => [
                'permission_type' => $data['permission_type'],
                'new_value' => $data['value'],
                'admin_user_id' => $adminScopeModel->admin_user_id,
                'division_id' => $adminScopeModel->division_id,
            ],
            'created_at' => now(),
        ]);

        $permissionName = $data['permission_type'] === 'can_manage_users' ? 'manage users' : 'assign roles';
        $status = $data['value'] ? 'enabled' : 'disabled';

        return redirect()->route('delegated-admins.index')
            ->with('success', "Permission to {$permissionName} has been {$status}.");
    }

    /**
     * Remove a delegated admin assignment.
     *
     * @param  int  $adminScope
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($adminScope)
    {
        $adminScopeModel = AdminScope::findOrFail($adminScope);
        $adminUserId = $adminScopeModel->admin_user_id;
        $divisionId = $adminScopeModel->division_id;

        // Log activity before deletion
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'delegated_admin_removed',
            'target_table' => 'admin_scopes',
            'target_id' => $adminScopeModel->id,
            'details' => [
                'admin_user_id' => $adminUserId,
                'division_id' => $divisionId,
            ],
            'created_at' => now(),
        ]);

        $adminScopeModel->delete();

        return redirect()->route('delegated-admins.index')
            ->with('success', 'Delegated admin assignment removed successfully.');
    }
}

