<?php

namespace Ssh521\LaravelAdmin\Http\Controllers\Api;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Ssh521\LaravelAdmin\Http\Controllers\Controller;
use Ssh521\LaravelAdmin\Http\Resources\UserResource;
use Ssh521\LaravelAdmin\Models\User;
use Ssh521\LaravelAdmin\Services\UserService;

class UserController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private readonly UserService $userService,
    ) {}

    /**
     * Display a listing of the users.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $this->authorize('viewAny', User::class);

        $users = $this->userService->getUserList([
            'search' => $request->input('search'),
            'role' => $request->input('role'),
            'sortField' => $request->input('sortField', 'name'),
            'sortDirection' => $request->input('sortDirection', 'asc'),
            'perPage' => $request->input('perPage', 15),
        ]);

        return UserResource::collection($users);
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = $this->userService->createUser([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => $request->input('password'),
        ]);

        return (new UserResource($user))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Display the specified user.
     */
    public function show(int $id): UserResource
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('view', $user);

        return new UserResource($user->load(['roles', 'permissions']));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, int $id): UserResource
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('update', $user);

        $updateData = [
            'userId' => $id,
            'name' => $request->input('name'),
            'email' => $request->input('email'),
        ];

        if ($request->has('password')) {
            $updateData['password'] = $request->input('password');
        }

        $user = $this->userService->updateUser($updateData);

        return new UserResource($user->load(['roles', 'permissions']));
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(int $id): JsonResponse
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('delete', $user);

        $this->userService->deleteUser(['userId' => $id]);

        return response()->json([
            'message' => 'User deleted successfully',
        ], 200);
    }

    /**
     * Assign roles to the specified user.
     */
    public function assignRoles(Request $request, int $id): UserResource
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('assignRoles', $user);

        $user = $this->userService->assignRolesToUser([
            'userId' => $id,
            'roleIds' => $request->input('roleIds', []),
        ]);

        return new UserResource($user->load(['roles', 'permissions']));
    }

    /**
     * Get roles of the specified user.
     */
    public function getRoles(int $id): JsonResponse
    {
        $user = $this->userService->getUserDetail(['userId' => $id]);
        $this->authorize('view', $user);

        return response()->json([
            'data' => $user->roles->map(function ($role) {
                return [
                    'id' => $role->id,
                    'name' => $role->name,
                ];
            }),
        ]);
    }
}
