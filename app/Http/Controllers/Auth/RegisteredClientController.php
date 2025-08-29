<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Representative;
use Illuminate\Validation\Rule;
use Throwable;
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
        return view('auth.clients.register');
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
            // La validación de 'document_id' debe ser única para la tabla de representantes
            'document_id' => [
                'required',
                'string',
                'max:15',
                'regex:/^[A-Za-z]{0,1}[0-9]{7,9}[A-Za-z]{1}$/',
                'unique:' . Representative::class,
            ],
            // La validación de 'email' debe ser única en la tabla de usuarios.
            // Si quieres permitir que un usuario existente se convierta en representante,
            // debes cambiar esta validación a una verificación manual después.
            'email' => [
                'required',
                'string',
                'email',
                'max:100',
            ],
            'phone' => ['required', 'string', 'regex:/^[0-9]{9,13}$/', 'max:15'],
            'occupation' => ['nullable', 'string', 'max:50'],
            'address' => ['required', 'string'],
            'sex' => ['required', 'string', 'max:15'],
            'birth_date' => ['required', 'date_format:d/m/Y'],
        ]);

        try {
            // 2. Usar una transacción para asegurar la integridad de los datos.
            $representative = DB::transaction(function () use ($request) {
                // Usa 'firstOrCreate' para encontrar un usuario existente o crear uno nuevo
                // basado en el email.
                $user = User::firstOrCreate(
                    ['email' => strtolower($request->email)],
                    [
                        'name' => strtoupper($request->name),
                        'last_name' => strtoupper($request->last_name),
                        'sex' => $request->sex,
                        'is_active' => false,
                    ]
                );

                // Si el usuario ya existe, actualizamos su nombre y apellido.
                if (!$user->wasRecentlyCreated) {
                    $user->update([
                        'name' => strtoupper($request->name),
                        'last_name' => strtoupper($request->last_name),
                        'sex' => $request->sex,
                    ]);
                }

                // Verifica si el usuario ya tiene el rol de representante.
                if ($user->hasRole('Representante')) {
                    // Lanza una excepción para que la transacción falle y el catch la capture.
                    throw new \Exception('Este usuario ya es un representante.');
                }

                // Asigna el rol 'Representante' al usuario si no lo tiene
                $user->assignRole('Representante');

                // Crea y guarda el registro del representante.
                $representative = Representative::create([
                    'user_id' => $user->id,
                    'document_id' => strtoupper($request->document_id),
                    'phone' => $request->phone,
                    'occupation' => strtoupper($request->occupation),
                    'address' => strtoupper($request->address),
                    'birth_date' => $request->birth_date,
                    'is_active' => true,
                ]);

                // Dispara el evento de usuario registrado.
                event(new Registered($user));

                // Devuelve el objeto representante para que la variable fuera de la transacción lo capture.
                return $representative;
            });
            
            // 3. Redirige solo si la transacción fue exitosa.
            return redirect()->route('students.create', ['representative' => $representative->id])->with('status', '¡Representante registrado con éxito!');

        } catch (Throwable $e) {
            // Captura la excepción y redirige con el mensaje de error.
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }
    }
}
