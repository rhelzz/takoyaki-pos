<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,cashier',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'is_active' => true,
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|in:admin,manager,cashier',
            'is_active' => 'required|boolean',
        ]);

        $data = [
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
            'is_active' => $request->is_active,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'User berhasil diperbarui');
    }

    public function destroy(User $user)
    {
        // Jangan biarkan admin menghapus dirinya sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menghapus akun sendiri');
        }

        $user->delete();
        
        return redirect()->route('users.index')->with('success', 'User berhasil dihapus');
    }

    public function toggleStatus(User $user)
    {
        // Jangan biarkan admin menonaktifkan dirinya sendiri
        if ($user->id === Auth::id()) {
            return redirect()->route('users.index')->with('error', 'Anda tidak dapat menonaktifkan akun sendiri');
        }

        $user->update(['is_active' => !$user->is_active]);
        
        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('users.index')->with('success', "User berhasil {$status}");
    }
}
