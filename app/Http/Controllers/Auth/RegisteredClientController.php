<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Representative;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;

class RegisteredClientController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register-client');
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
            'document_id' => ['required', 'string', 'max:15', 'regex:/^[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}$/', 'unique:' . Representative::class],
            'email' => ['required', 'string', 'email', 'max:100', 'unique:' . User::class],
            'phone' => ['required', 'string', 'regex:/^[0-9]{9,13}$/', 'max:15'],
            'occupation' => ['string', 'max:50'],
            'address' => ['required', 'string'],
            'sex' => ['required', 'string', 'max:15'],
            'birth_date' => ['required', 'date_format:d/m/Y'],
        ]);

        // Usar una transacción para asegurar que ambos registros se creen o ninguno se cree
        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => strtoupper($request->name),
                'last_name' => strtoupper($request->last_name),
                'email' => strtolower($request->email),
                'sex' => $request->sex,
                'is_active' => false,
            ]);
            // Asignación del rol
            $user->assignRole('Representante');

            // Crear y guardar el representative
            $representative = Representative::create([
                'user_id' => $user->id, // Relacion con la tabla users
                'document_id' => strtoupper($request->document_id),
                'phone' => $request->phone,
                'address' => strtoupper($request->address),
                'birth_date' => $request->birth_date,
                'is_active' => true,
            ]);
            event(new Registered($user));
        });
        return redirect()->route('dashboard')->with('status', '¡Representante registrado con éxito!');
    }
}
