<?php

namespace App\Domains\Identity\Http\Requests\Api\Auth;

use App\Domains\Identity\Models\User;
use App\Domains\Identity\Repositories\UserRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TokenRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->email) {
            $this->merge(['email' => strtolower(trim($this->email))]);
        }
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
            'deviceName' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function attributes(): array
    {
        return [
            'email' => 'correo electrónico',
            'password' => 'contraseña',
            'deviceName' => 'nombre del dispositivo',
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico no es válido.',
            'password.required' => 'La contraseña es obligatoria.',
        ];
    }

    public function authenticate(): User
    {
        $email = $this->validated('email');
        $password = $this->validated('password');

        $user = app(UserRepository::class)->findByEmail($email);

        if (! $user) {
            Hash::make($password);
        }

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => 'Las credenciales proporcionadas son incorrectas.',
            ]);
        }

        if (! $user->canAuthenticate()) {
            throw ValidationException::withMessages([
                'email' => 'Tu cuenta está inactiva, contacta con el administrador.',
            ]);
        }

        return $user;
    }

    public function deviceName(): string
    {
        return $this->validated('deviceName') ?: 'api';
    }
}
