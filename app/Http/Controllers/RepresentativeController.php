<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Representative;
use Spatie\Permission\Models\Role;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Throwable;
use Illuminate\Support\Facades\Auth;

class RepresentativeController extends Controller
{
    use AuthorizesRequests;
    /**
     * Get the currently authenticated user.
     *
     * @return User
     */
    protected function currentUser(): User
    {
        return Auth::user();
    }

    /**
     * Display a listing of the representatives.
     *
     * @param Request $request
     * @return View|RedirectResponse
     */
    public function index(Request $request): View|RedirectResponse
    {
        try {
            $this->authorize('viewAny', Representative::class);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        $representatives = collect(); // Initialize an empty collection for representatives
        $search = trim($request->input('search', '')); // Trim whitespace from search input

        if ($search !== '') {
            $query = Representative::with(['user.roles'])
                ->whereHas('user', function ($q) use ($search, $request) {
                    $q->role('Representante')
                        ->where(function ($q2) use ($search) {
                            $q2->where('name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });

                    // Filter by status if provided
                    if ($request->filled('status') && $request->input('status') !== 'Todos') {
                        $isActive = $request->input('status') === 'Activo' ? 1 : 0;
                        $q->where('is_active', $isActive);
                    }
                })

                ->join('users', 'representatives.user_id', '=', 'users.id') // User join
                ->orderBy('users.name', 'asc') // Order by user name ascending
                ->select('representatives.*'); // Select representatives columns

            $representatives = $query->paginate(6); // Paginate results, 6 per page
        }

        return view('representatives.index', compact('representatives'));
    }

    /**
     * Display the specified representative.
     *
     * @param Representative $representative
     * @return View|RedirectResponse
     */
    public function show(Representative $representative): View|RedirectResponse
    {
        try {
            $this->authorize('view', $representative);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('representatives.show', compact('representative'));
    }

    /**
     * Show the form for creating a new representative.
     *
     * @return View|RedirectResponse
     */
    public function create(): View|RedirectResponse
    {
        try {
            $this->authorize('create', User::class);
        } catch (AuthorizationException $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return view('representatives.create');
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
