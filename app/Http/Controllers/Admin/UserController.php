<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Services\UserService;
use App\Models\Kabupaten;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $service;

    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of users.
     */
    public function index(Request $request)
    {
        $filters = $request->only(['search']);
        $users = $this->service->getUsersPaginated(10, $filters);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        $roles = Role::all();
        $kabupatens = Kabupaten::orderBy('nama')->get();

        return view('admin.users.create', compact('roles', 'kabupatens'));
    }

    /**
     * Store a newly created user.
     */
    public function store(StoreUserRequest $request)
    {
        try {
            // Add hardcoded kabupaten_id for now
            $data = $request->validated();
            $data['kabupaten_id'] = '6372';

            $user = $this->service->createUser($data);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User berhasil dibuat');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit($id)
    {
        try {
            $user = $this->service->getUserById($id);
            $roles = Role::all();
            $kabupatens = Kabupaten::orderBy('nama')->get();

            return view('admin.users.edit', compact('user', 'roles', 'kabupatens'));
        } catch (\Exception $e) {
            return redirect()
                ->route('admin.users.index')
                ->with('error', 'User tidak ditemukan');
        }
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $this->service->updateUser($id, $request->validated());

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy($id)
    {
        try {
            // Business validation: prevent deleting last super admin
            $user = $this->service->getUserById($id);

            if ($user->hasRole('super_admin') && \App\Models\User::role('super_admin')->count() <= 1) {
                return redirect()
                    ->back()
                    ->with('error', 'Tidak dapat menghapus Super Admin terakhir');
            }

            $this->service->deleteUser($id);

            return redirect()
                ->route('admin.users.index')
                ->with('success', 'User berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
