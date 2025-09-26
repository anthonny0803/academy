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
    public function create(Representative $representative): View|RedirectResponse
    {

        if (!$representative->user->hasRole('Representante')) {
            return redirect()->back()
                ->with('error', 'El usuario seleccionado no es un representante válido.');
        }

        return view('students.create', ['representative' => $representative]);
    }

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

        $representative = DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => strtoupper($request->name),
                'last_name' => strtoupper($request->last_name),
                'email' => strtolower($request->email),
                'sex' => $request->sex,
                'is_active' => false,
            ]);

            $user->assignRole('Representante');
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

            return $representative;
        });
        return redirect()->route('students.create', ['representative' => $representative->id])->with('success', '¡Estudiante registrado con éxito!');
    }
}
