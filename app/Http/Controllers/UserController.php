<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class UserController extends Controller
{
    /**
     * Display the listing view.
     */
    public function index(Request $request): View
    {
        $query = User::with('roles'); // eager load de roles para evitar N+1

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $users = $request->filled('search') ? $query->paginate(8) : collect();

        return view('users.index', compact('users'));
    }

    /**
     * Display the specified user.
     */
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    /**
     * Display the creation view.
     */
    public function create(): View
    {
        return view('users.create');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:' . User::class],
            'sex' => ['required', 'string', 'max:15'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user = User::create([
            'name' => strtoupper($request->name),
            'last_name' => strtoupper($request->last_name),
            'email' => strtolower($request->email),
            'sex' => $request->sex,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        // Role assignment
        $roleName = $request->input('role');
        $user->assignRole($roleName);

        event(new Registered($user));

        return redirect()->route('dashboard')->with('status', '¡Usuario registrado con éxito!');
    }

    /**
     * Display the edit user view.
     */
    public function edit(User $user): View
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'last_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:' . User::class . ',' . $user->id],
            'sex' => ['required', 'string', 'max:15'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $user->update([
            'name' => strtoupper($request->name),
            'last_name' => strtoupper($request->last_name),
            'email' => strtolower($request->email),
            'sex' => $request->sex,
            'password' => $request->filled('password') ? Hash::make($request->password) : $user->password,
            'is_active' => true,
        ]);

        // Role assignment
        $roleName = $request->input('role');
        $user->syncRoles([$roleName]);

        return redirect()->route('dashboard')->with('status', '¡Usuario actualizado con éxito!');
    }
}
