<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $users = User::with('role', 'division')
            ->orderBy('user_id', 'desc')
            ->paginate(20);
        
        return view('users.index', compact('users'));
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
            'full_name' => 'required|string|max:200',
            'username' => 'required|string|max:100|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'division_id' => 'required|exists:divisions,division_id',
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'sometimes|in:active,inactive,pending'
        ]);

        $user = User::create([
            'full_name' => $data['full_name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password_hash' => Hash::make($data['password']),
            'division_id' => $data['division_id'],
            'role_id' => $data['role_id'],
            'status' => $data['status'] ?? 'active',
        ]);

        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Created new user: ' . $user->full_name,
                'timestamp' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

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
            'full_name' => 'required|string|max:200',
            'username' => 'required|string|max:100|unique:users,username,' . $user->user_id . ',user_id',
            'email' => 'required|email|unique:users,email,' . $user->user_id . ',user_id',
            'password' => 'nullable|min:8|confirmed',
            'division_id' => 'required|exists:divisions,division_id',
            'role_id' => 'required|exists:roles,role_id',
            'status' => 'sometimes|in:active,inactive,pending'
        ]);

        $user->full_name = $data['full_name'];
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->division_id = $data['division_id'];
        $user->role_id = $data['role_id'];
        $user->status = $data['status'] ?? $user->status;
        
        if (!empty($data['password'])) {
            $user->password_hash = Hash::make($data['password']);
        }
        
        $user->save();

        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Updated user: ' . $user->full_name,
                'timestamp' => now(),
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified user from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $userName = $user->full_name;
        $user->delete();

        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Deleted user: ' . $userName,
                'timestamp' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }

    /**
     * Toggle user active status.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(User $user)
    {
        $user->status = ($user->status === 'active') ? 'inactive' : 'active';
        $user->save();

        if (Auth::check()) {
            UserActivityLog::create([
                'user_id' => Auth::id(),
                'activity' => 'Changed status of user ' . $user->full_name . ' to ' . $user->status,
                'timestamp' => now(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User status changed successfully.');
    }
}
