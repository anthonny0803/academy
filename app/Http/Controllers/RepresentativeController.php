<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Representative;

class RepresentativeController extends Controller
{
    // Mostrar lista de Representantes
    public function index()
    {
        $representatives = Representative::paginate(15);
        return view('representatives.index', compact('representatives'));
    }

    // Mostrar Estudiantes de un Representante por ID
    public function students($id)
    {
        $representative = Representative::with('students')->findOrFail($id);
        $students = $representative->students;
        return view('representatives.students.index', compact('representative', 'students'));
    }

    // Mostrar formulario para crear un nuevo Representante
    public function create()
    {
        return view('representatives.create');
    }

    // Registrar Representante
    public function store(Request $request)
    {
        // Validaciones básicas
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'document' => 'required|string|max:15|unique:representatives,document',
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:30|unique:representatives,email',
            'birth_date' => 'required|date',
            'address' => 'required|string|max:255',
            'relationship' => 'required|string|max:15',
        ]);

        // Registrar
        Representative::create($validated);
        return redirect()->route('representatives.index')->with('success', 'Representante creado correctamente.');
    }

    // Mostrar formulario para actualizar el Representante
    public function edit(Representative $representative)
    {
        return view('representatives.edit', compact('representative'));
    }

    // Actualizar Representante
    public function update(Request $request, Representative $representative)
    {

        // Validaciones básicas
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'document' => 'required|string|max:15|unique:representatives,document,' . $representative->id,
            'phone' => 'required|string|max:15',
            'email' => 'required|email|max:30|unique:representatives,email,' . $representative->id,
            'birth_date' => 'required|date',
            'address' => 'required|string|max:255',
            'relationship' => 'required|string|max:15',
        ]);

        // Actualizar
        $representative->update($validated);
        return redirect()->route('representatives.index')->with('success', 'Representante actualizado correctamente.');
    }

    // Eliminar Representante
    public function destroy(Representative $representative)
    {
        $representative->delete();
        return redirect()->route('representatives.index')->with('success', 'Representante eliminado.');
    }
}
