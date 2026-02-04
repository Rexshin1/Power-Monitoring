<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = \App\Models\User::latest()->paginate(10);
        return view('pages.users.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'role' => 'required|in:admin,user',
            'password' => 'required|string|min:6',
        ]);

        $validated['password'] = bcrypt($validated['password']);
        
        \App\Models\User::create($validated);

        return redirect()->back()->with('success', 'User added successfully.');
    }

    public function update(Request $request, string $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        $rules = [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$user->id,
            'role' => 'required|in:admin,user',
        ];

        // Only validate password if it is provided (not empty)
        if ($request->filled('password')) {
            $rules['password'] = 'required|string|min:6';
        }

        $validated = $request->validate($rules);

        if ($request->filled('password')) {
            $validated['password'] = bcrypt($validated['password']);
        } else {
            unset($validated['password']); // Do not update password if empty
        }

        $user->update($validated);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy(string $id)
    {
        if (auth()->id() == $id) {
            return redirect()->back()->with('error', 'You cannot delete yourself!');
        }

        $user = \App\Models\User::findOrFail($id);
        $user->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}
