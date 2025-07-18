<?php

namespace App\Http\Controllers;

use App\Models\Representative;
use App\Models\Section;
use Illuminate\Http\Request;
use App\Models\Student;

class StudentController extends Controller
{
    // Mostrar lista de Estudiantes
    public function index()
    {
        $students = Student::paginate(15);
        return view('students.index', compact('students'));
    }

    // Mostrar el Representante del Estudiante
    public function showRepresentative($id)
    {
        $student = Student::with('representative')->findOrFail($id);
        $representative = $student->representative;
        return view('students.representative', compact('student', 'representative'));
    }

    // Mostrar formulario para crear un nuevo Estudiante
    public function create(Representative $representative)
    {
        $sections = Section::all();
        return view('students.create', compact('representative', 'sections'));
    }

    // Registrar Estudiante
    public function store(Request $request, Representative $representative)
    {
        // Validaciones básicas
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'birth_date' => 'required|date',
            'email' => 'nullable|email|max:30',
            'phone' => 'required|string|max:15',
            'document' => 'required|string|max:15|unique:students,document',
            'address' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:30',
            'mother_name' => 'nullable|string|max:30',
            'father_last_name' => 'nullable|string|max:30',
            'mother_last_name' => 'nullable|string|max:30',
            'section_id' => 'required|exists:sections,id'
        ]);

        // Agregamos el ID del Representante correspondiente antes de registrar
        $validated['representative_id'] = $representative->id;

        // Registrar Estudiante
        $student = Student::create($validated);
        return redirect()->route('students.index')->with('success', 'Estudiante creado correctamente.');
    }

    // Mostrar formulario para actualizar el Estudiante
    public function edit(Student $student)
    {
        $sections = Section::all();
        return view('students.edit', compact('student', 'sections'));
    }

    // Actualizar Estudiante
    public function update(Request $request, Student $student)
    {

        // Validaciones básicas
        $validated = $request->validate([
            'name' => 'required|string|max:30',
            'last_name' => 'required|string|max:30',
            'birth_date' => 'required|date',
            'email' => 'nullable|email|max:30',
            'phone' => 'required|string|max:15',
            'document' => 'required|string|max:15|unique:students,document,' . $student->id,
            'address' => 'required|string|max:255',
            'father_name' => 'nullable|string|max:30',
            'mother_name' => 'nullable|string|max:30',
            'father_last_name' => 'nullable|string|max:30',
            'mother_last_name' => 'nullable|string|max:30',
            'section_id' => 'required|exists:sections,id'
        ]);

        // Actualizar
        $student->update($validated);
        return redirect()->route('students.index')->with('success', 'Estudiante actualizado correctamente.');
    }

    // Eliminar Estudiante
    public function destroy(Student $student){
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Estudiante eliminado.');
    }
}
