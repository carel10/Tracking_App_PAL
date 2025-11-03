<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\AuthSession;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the users with search and filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'division']);

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by division
        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by role (via pivot table)
        if ($request->filled('role_id')) {
            $query->whereHas('roles', function($q) use ($request) {
                $q->where('roles.id', $request->role_id);
            });
        }

        $users = $query->orderBy('created_at', 'desc')->paginate(20)->withQueryString();
        $roles = Role::all();
        $divisions = Division::all();

        return view('users.index', compact('users', 'roles', 'divisions'));
    }

    /**
     * Show the form for creating a new user.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::all();
        $divisions = Division::all();
        
        return view('users.form', compact('roles', 'divisions'));
    }

    /**
     * Store a newly created user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'division_id' => 'required|exists:divisions,id',
            'status' => 'sometimes|in:active,inactive,pending',
            'role_ids' => 'sometimes|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'division_id' => $data['division_id'],
            'status' => $data['status'] ?? 'pending',
        ]);

        // Assign roles
        if (!empty($data['role_ids'])) {
            $user->roles()->sync($data['role_ids']);
        }

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'user_created',
            'target_table' => 'users',
            'target_id' => $user->id,
            'details' => [
                'user_name' => $user->full_name,
                'email' => $user->email,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Show the form for editing the specified user.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        $user->load('roles');
        $roles = Role::all();
        $divisions = Division::all();
        
        return view('users.form', compact('user', 'roles', 'divisions'));
    }

    /**
     * Update the specified user in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
            'password' => 'nullable|min:8|confirmed',
            'division_id' => 'required|exists:divisions,id',
            'status' => 'sometimes|in:active,inactive,pending',
            'role_ids' => 'sometimes|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user->full_name = $data['full_name'];
        $user->email = $data['email'];
        $user->division_id = $data['division_id'];
        $user->status = $data['status'] ?? $user->status;
        
        if (!empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
        }
        
        $user->save();

        // Update roles
        if ($request->has('role_ids')) {
            $user->roles()->sync($data['role_ids'] ?? []);
        }

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'user_updated',
            'target_table' => 'users',
            'target_id' => $user->id,
            'details' => [
                'user_name' => $user->full_name,
                'changes' => $data,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage (soft delete).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $userName = $user->full_name;
        
        // Soft delete: set status to inactive instead of deleting
        $user->status = 'inactive';
        $user->save();

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'user_deleted',
            'target_table' => 'users',
            'target_id' => $user->id,
            'details' => [
                'user_name' => $userName,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User archived successfully.');
    }

    /**
     * Toggle user active status (activate/suspend).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(User $user)
    {
        $oldStatus = $user->status;
        $user->status = ($user->status === 'active') ? 'suspended' : 'active';
        $user->save();

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'user_status_changed',
            'target_table' => 'users',
            'target_id' => $user->id,
            'details' => [
                'user_name' => $user->full_name,
                'old_status' => $oldStatus,
                'new_status' => $user->status,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User status changed successfully.');
    }

    /**
     * Assign roles to user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assignRoles(Request $request, User $user)
    {
        $data = $request->validate([
            'role_ids' => 'required|array',
            'role_ids.*' => 'exists:roles,id',
        ]);

        $user->roles()->sync($data['role_ids']);

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'user_roles_assigned',
            'target_table' => 'users',
            'target_id' => $user->id,
            'details' => [
                'user_name' => $user->full_name,
                'role_ids' => $data['role_ids'],
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Roles assigned successfully.');
    }

    /**
     * Reset user password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resetPassword(Request $request, User $user)
    {
        $data = $request->validate([
            'password' => 'required|min:8|confirmed',
        ]);

        $user->password_hash = Hash::make($data['password']);
        $user->save();

        // Invalidate all user sessions (force logout)
        AuthSession::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'user_password_reset',
            'target_table' => 'users',
            'target_id' => $user->id,
            'details' => [
                'user_name' => $user->full_name,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'Password reset successfully. User has been logged out from all devices.');
    }

    /**
     * View user sessions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\View\View
     */
    public function viewSessions(User $user)
    {
        $sessions = AuthSession::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->orderBy('issued_at', 'desc')
            ->get();

        return view('users.sessions', compact('user', 'sessions'));
    }

    /**
     * Force logout user from all sessions.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function forceLogout(User $user)
    {
        // Expire all active sessions
        AuthSession::where('user_id', $user->id)
            ->where('expires_at', '>', now())
            ->update(['expires_at' => now()]);

        // Log activity
        AuditLog::create([
            'actor_user_id' => Auth::id(),
            'action' => 'user_force_logout',
            'target_table' => 'users',
            'target_id' => $user->id,
            'details' => [
                'user_name' => $user->full_name,
            ],
            'created_at' => now(),
        ]);

        return redirect()->route('users.index')
            ->with('success', 'User has been logged out from all devices.');
    }
}
