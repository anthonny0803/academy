<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Subject;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Traits\AuthorizesRedirect;

class SubjectController extends Controller
{
    use AuthorizesRequests, AuthorizesRedirect;

    protected function currentUser(): User
    {
        return Auth::user();
    }

    /**
     * Display a listing of subjects.
     */
    public function index(Request $request): View|RedirectResponse
    {
        return $this->authorizeOrRedirect('viewAny', Subject::class, function () use ($request) {
            $search = $request->input('search', '');

            $query = $search !== '' ? Subject::search($search) : Subject::query();
            $subjects = $query->paginate(6);

            return view('subjects.index', compact('subjects'));
        });
    }
}
