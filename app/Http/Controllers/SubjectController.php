<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\StoreSubjectRequest;
use App\Http\Requests\UpdateSubjectRequest;
use App\Services\StoreSubjectService;
use App\Services\UpdateSubjectService;
use App\Traits\AuthorizesRedirect;

class SubjectController extends Controller
{
    use AuthorizesRequests, AuthorizesRedirect;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', Subject::class, function () use ($request) {
            $search = $request->input('search', '');

            $query = $search !== '' ? Subject::search($search) : Subject::query();
            $subjects = $query->paginate(5);

            return view('subjects.index', compact('subjects'));
        });
    }

    public function store(StoreSubjectRequest $request, StoreSubjectService $storeService)
    {
        try {
            $storeService->handle($request->validated());
            return redirect()->route('subjects.index')
                ->with('status', '¡Asignatura registrada correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function update(UpdateSubjectRequest $request, UpdateSubjectService $updateService, Subject $subject)
    {
        try {
            $subject = $updateService->handle($subject, $request->validated());
            return redirect()->route('subjects.index')
                ->with('status', '¡Asignatura actualizada correctamente!');
        } catch (\Exception $e) {
            return redirect()->back()->withInput()
                ->with('error', $e->getMessage());
        }
    }
}
