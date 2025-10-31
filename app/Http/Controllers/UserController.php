<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\Division;
use App\Models\UserActivityLog;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles','division')->orderBy('id','desc')->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        $divisions = Division::all();
        return view('users.form', compact('roles','divisions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|confirmed',
            'division_id' => 'nullable|exists:divisions,id',
            'roles' => 'nullable|array',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'division_id' => $data['division_id'] ?? null,
            'is_active' => true,
        ]);

        if (!empty($data['roles'])) {
            $user->roles()->sync($data['roles']);
        }

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'create_user',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => ['created_user_id' => $user->id],
        ]);

        return redirect()->route('users.index')->with('success', 'User created');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        $divisions = Division::all();
        return view('users.form', compact('user','roles','divisions'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
            'division_id' => 'nullable|exists:divisions,id',
            'roles' => 'nullable|array',
            'is_active' => 'sometimes|boolean',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        if (!empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }
        $user->division_id = $data['division_id'] ?? null;
        if (isset($data['is_active'])) $user->is_active = $data['is_active'];
        $user->save();

        $user->roles()->sync($data['roles'] ?? []);

        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'update_user',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => ['updated_user_id' => $user->id],
        ]);

        return redirect()->route('users.index')->with('success','User updated');
    }

    public function destroy(User $user)
    {
        $user->delete();
        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => 'delete_user',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => ['deleted_user_id' => $user->id],
        ]);
        return redirect()->route('users.index')->with('success','User deleted');
    }

    public function toggleActive(User $user)
    {
        $user->is_active = !$user->is_active;
        $user->save();
        UserActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $user->is_active ? 'activate_user' : 'deactivate_user',
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => ['toggled_user_id' => $user->id],
        ]);
        return redirect()->route('users.index')->with('success','User status changed');
    }
}
