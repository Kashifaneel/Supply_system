<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class UserController extends Controller
{    public function index()
    {
        $users = User::latest()->get();
        return Inertia::render('Users/Index', [
            'users' => $users
        ]);
    }

    public function summary()
    {
        $userCount = User::count();
        $adminCount = User::where('role', 'Admin')->count();
        $regularUserCount = User::where('role', 'User')->count();

        return Inertia::render('Users/AdminSummary', [
            'userCount' => $userCount,
            'adminCount' => $adminCount,
            'regularUserCount' => $regularUserCount
        ]);
    }

    public function create()
    {
        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:Admin,User'
        ]);

        $validated['password'] = bcrypt($validated['password']);
        User::create($validated);
        
        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }    public function edit(User $user)
    {
        return Inertia::render('Users/EditNew', [
            'user' => $user
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'password' => 'nullable|string|min:8',
            'role' => 'required|in:Admin,User'
        ]);

        $user->update($validated);
        return redirect()->route('users.index');
    }

    public function destroy(User $user)
    {
        $this->authorize('delete', $user);
        $user->delete();
        return redirect()->route('users.index');
    }
}
