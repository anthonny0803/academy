<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Representative;
use App\Models\Student;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Representative $representative): View
    {
        return view('students.create', ['representative' => $representative]);
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
        $representative = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => strtoupper($request->name),
                'last_name' => strtoupper($request->last_name),
                'email' => strtolower($request->email),
                'sex' => $request->sex,
                'is_active' => false,
            ]);
            // Asignación del rol
            $user->assignRole('Representante');

            // Crear y guarda el representante
            $representative = Representative::create([
                'user_id' => $user->id, // Relacion con la tabla users
                'document_id' => strtoupper($request->document_id),
                'phone' => $request->phone,
                'occupation' => strtoupper($request->occupation),
                'address' => strtoupper($request->address),
                'birth_date' => $request->birth_date,
                'is_active' => true,
            ]);
            event(new Registered($user));

            // Retorna el valor del representante para poder usarlo
            return $representative;
        });
        return redirect()->route('students.create', ['representative' => $representative->id])->with('status', '¡Estudiante registrado con éxito!');
    }
}
