<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kabupaten;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $users = User::with(['roles', 'kabupaten', 'kecamatan', 'kelurahan'])
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $roles = Role::all();
        $kabupatens = Kabupaten::orderBy('nama')->get();
        return view('admin.users.create', compact('roles', 'kabupatens'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'kabupaten_id' => 'nullable|exists:m_kabupatens,id',
            'kecamatan_id' => 'nullable|exists:m_kecamatans,id',
            'kelurahan_id' => 'nullable|exists:m_kelurahans,id',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'kabupaten_id' => $request->kabupaten_id,
            'kecamatan_id' => $request->kecamatan_id,
            'kelurahan_id' => $request->kelurahan_id,
        ]);

        $user->assignRole($request->role);

        return redirect()->route('admin.users.index')->with('success', 'User created successfully');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $kabupatens = Kabupaten::orderBy('nama')->get();

        return view('admin.users.edit', compact('user', 'roles', 'kabupatens'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'role' => 'required|exists:roles,name',
            'kabupaten_id' => 'nullable|exists:m_kabupatens,id',
            'kecamatan_id' => 'nullable|exists:m_kecamatans,id',
            'kelurahan_id' => 'nullable|exists:m_kelurahans,id',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        // Only update location keys if logic allows, simplified here to just update what's sent
        // Logic to clear lower-level location IDs if higher-level changes is handled in specific logic or ignored for flexibility
        $user->kabupaten_id = $request->kabupaten_id;
        $user->kecamatan_id = $request->kecamatan_id;
        $user->kelurahan_id = $request->kelurahan_id;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();
        $user->syncRoles([$request->role]);

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('super_admin') && User::role('super_admin')->count() <= 1) {
            return back()->with('error', 'Cannot delete the last Super Admin');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted successfully');
    }
}
