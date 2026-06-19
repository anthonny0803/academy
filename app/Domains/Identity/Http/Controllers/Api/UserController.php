<?php

namespace App\Domains\Identity\Http\Controllers\Api;

use App\Domains\Identity\Http\Requests\Api\Users\StoreUserRequest;
use App\Domains\Identity\Http\Requests\Api\Users\UpdateUserRequest;
use App\Domains\Identity\Http\Resources\UserResource;
use App\Domains\Identity\Models\User;
use App\Domains\Identity\Repositories\UserRepository;
use App\Domains\Identity\Services\Users\DeleteUserService;
use App\Domains\Identity\Services\Users\StoreUserService;
use App\Domains\Identity\Services\Users\UpdateUserService;
use App\Domains\Shared\Http\Controllers\Controller;
use App\Domains\Shared\Traits\RespondsWithResources;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    use AuthorizesRequests;
    use RespondsWithResources;

    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function index(Request $request): JsonResponse
    {
        $this->authorize('viewAny', User::class);

        $search = trim((string) $request->input('search', ''));
        $isActive = $request->has('isActive') ? $request->boolean('isActive') : null;
        $role = $request->input('role');

        $users = $this->userRepository->paginateEmployees($search, $isActive, $role, $this->resolvePerPage($request));

        return $this->paginatedResponse($users, UserResource::class);
    }

    public function show(User $user): UserResource
    {
        $this->authorize('view', $user);

        return new UserResource($user->load('roles'));
    }

    public function store(StoreUserRequest $request, StoreUserService $storeService): JsonResponse
    {
        $this->authorize('create', User::class);

        $user = $storeService->handle($request->validated());

        return (new UserResource($user))->response()->setStatusCode(Response::HTTP_CREATED);
    }

    public function update(UpdateUserRequest $request, UpdateUserService $updateService, User $user): UserResource
    {
        $this->authorize('update', $user);

        $user = $updateService->handle($user, $request->validated());

        return new UserResource($user);
    }

    public function destroy(User $user, DeleteUserService $deleteService): Response
    {
        $this->authorize('delete', $user);

        $deleteService->handle($user);

        return response()->noContent();
    }
}
