<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class CreateGameSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Delegates to CategoryPolicy::createSession(User $user).
        // Non-nullable User type-hint auto-denies guests before the policy runs.
        return Gate::allows('createSession', Category::class);
    }

    /**
     * Called automatically when authorize() returns false.
     * Redirects browsers to the login page with a flash message instead of
     * throwing a bare HTTP 403.
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            redirect()->route('auth.login')
                ->with('error', 'You must be logged in to create a game session.')
        );
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('session_name') && is_string($this->input('session_name'))) {
            $this->merge(['session_name' => trim($this->input('session_name'))]);
        }
    }

    public function rules(): array
    {
        return [
            'session_name' => ['required', 'string', 'min:2', 'max:30', 'regex:/^[a-zA-Z0-9 _-]+$/'],
            'difficulty'   => ['nullable', 'string', 'in:easy,medium,hard'],
        ];
    }

    public function messages(): array
    {
        return [
            'session_name.required' => 'Please enter a game session name.',
            'session_name.regex' => 'Session name may only contain letters, numbers, spaces, dashes, and underscores.',
        ];
    }
}
