<?php

namespace App\Http\Controllers;

use App\Models\Representative;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    // Mostrar lista de Estudiantes
    public function index()
    {
        $students = Student::all();
        return view('students.index', compact('students'));
    }

    // Mostrar el Representante del Estudiante
    public function showRepresentative($id)
    {
        $student = Student::with('representative')->findOrFail('$id');
        $representative = $student->representative;
        return view('students.representative', compact('student', 'representative'));
    }

    // Mostrar formulario para crear un nuevo Estudiante
    public function create(Representative $representative)
    {
        return view('students.create', compact('representative'));
    }

    // Registrar Estudiante
    public function store(Request $request)
    {
        // Validaciones básicas
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'birth_date' => 'required|date',
            'email' => 'string|max:30',
            'phone' => 'required|string|max:15',
            'document' => 'required|string|max:15|unique:students, document',
            'address' => 'required|string|max:255',
            'father_name' => 'string|max:30',
            'mother_name' => 'string|max:30',
            'father_last_name' => 'string|max:30',
            'mother_last_name' => 'string|max:30'
        ]);

        // Registrar Estudiante
        $student = Student::create($validated);
        return redirect()->route('students.index')->with('success', 'Estudiante creado correctamente.');
    }

    // Mostrar formulario para actualizar el Estudiante
    
}
