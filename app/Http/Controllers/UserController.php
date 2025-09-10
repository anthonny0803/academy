<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\RoleAssignmentService;
use App\Services\StoreUserService;
use App\Services\UpdateUserService;
use App\Services\UserActivationService;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;



class UserController extends Controller
{
    use AuthorizesRequests;

    /**
     * Get the currently authenticated user.
     */
    protected function currentUser(): User
    {
        return Auth::user();
    }

    /**
     * Display the listing view.
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
            // Authorization check
            $this->authorize('viewAny', User::class);

            // Search functionality
            $users = collect(); // Collection empty by default
            if ($request->filled('search')) {
                $search = $request->input('search');
                $users = User::with('roles')
                    ->where(function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('email', 'like', "%{$search}%");
                    })->paginate(6);
            }
            return view('users.index', compact('users'));
        }

        // If the user is not authorized, redirect with error 
        catch (AuthorizationException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View|RedirectResponse
    {
        try {
            // Authorization check
            $this->authorize('view', $user);

            // Show the user
            return view('users.show', compact('user'));
        }

        // If the user is not authorized, redirect with error
        catch (AuthorizationException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Display the creation view with assignable roles.
     */
    public function create(RoleAssignmentService $roleAssignmentService): View|RedirectResponse
    {
        try {
            // Authorization check
            $this->authorize('create', User::class);

            // Get assignable roles based on current user's role
            $roles = $roleAssignmentService->getAssignableRoles(Auth::user());

            // Show the creation view
            return view('users.create', compact('roles'));
        }

        // If the user is not authorized, redirect with error
        catch (AuthorizationException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(StoreUserRequest $request, StoreUserService $storeService): RedirectResponse
    {
        try {
            // Use the StoreUserService to create and return a new user instance
            $user = $storeService->handle($request->validated());

            // On success, redirect to the user's detail page with success message
            return redirect()->route('users.show', $user)
                ->with('status', '¡Usuario registrado con éxito!');
        }

        // Case of any error, redirect back with error message
        catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hubo un error al registrar el usuario.');
        }
    }

    /**
     * Display the edit user view.
     */
    public function edit(User $user, RoleAssignmentService $roleAssignmentService): View|RedirectResponse
    {
        try {
            // Authorization check
            $this->authorize('edit', $user);


            // Get assignable roles based on current user's role
            $roles = $roleAssignmentService->getAssignableRoles($this->currentUser());

            // Show the edit view
            return view('users.edit', compact('user', 'roles'));
        }

        // If the user is not authorized, redirect with error
        catch (AuthorizationException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update the specified user.
     */
    public function update(UpdateUserRequest $request, UpdateUserService $updateService, User $user): RedirectResponse
    {
        try {
            $user = $updateService->handle($user, $request->validated());

            return redirect()->route('users.show', $user)
                ->with('status', '¡Usuario actualizado con éxito!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Hubo un error al actualizar el usuario.');
        }
    }

    /**
     * Remove the specified user.
     */
    public function destroy(User $user): RedirectResponse
    {
        try {
            // Authorization check
            $this->authorize('delete', $user);

            // If the user has a representative profile, delete it first
            if ($user->representative?->exists()) {
                $user->representative()->delete();
            }

            // Delete the user
            $user->delete();
            return redirect()->route('users.index')
                ->with('status', '¡Usuario eliminado con éxito!');
        }

        // If the user is not authorized, redirect with error
        catch (AuthorizationException $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Toggle the activation status of the specified user.
     */
    public function toggleActivation(User $user, UserActivationService $activationService): RedirectResponse
    {
        try {
            $status = $activationService->toggle($user);
            return redirect()->route('users.show', $user)
                ->with('status', "¡Usuario {$status} con éxito!");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
