<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserCollection;
use App\Services\UserService;
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
     * GET /api/v1/users
     */
    public function index(Request $request)
    {
        try {
            $perPage = $request->get('per_page', 15);
            $filters = $request->only(['search', 'role', 'kabupaten_id', 'kecamatan_id', 'kelurahan_id', 'sort_by', 'sort_order']);

            $users = $this->service->getUsersPaginated($perPage, $filters);

            return new UserCollection($users);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengambil data users',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created user.
     * POST /api/v1/users
     */
    public function store(StoreUserRequest $request)
    {
        try {
            $user = $this->service->createUser($request->validated());

            return (new UserResource($user))
                ->additional([
                    'success' => true,
                    'message' => 'User berhasil dibuat'
                ])
                ->response()
                ->setStatusCode(201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal membuat user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified user.
     * GET /api/v1/users/{id}
     */
    public function show($id)
    {
        try {
            $user = $this->service->getUserById($id);

            return new UserResource($user);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan',
                'error' => $e->getMessage()
            ], 404);
        }
    }

    /**
     * Update the specified user.
     * PUT/PATCH /api/v1/users/{id}
     */
    public function update(UpdateUserRequest $request, $id)
    {
        try {
            $user = $this->service->updateUser($id, $request->validated());

            return (new UserResource($user))
                ->additional([
                    'success' => true,
                    'message' => 'User berhasil diupdate'
                ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal update user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified user.
     * DELETE /api/v1/users/{id}
     */
    public function destroy($id)
    {
        try {
            $this->service->deleteUser($id);

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
