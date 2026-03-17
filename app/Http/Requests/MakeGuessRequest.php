<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class MakeGuessRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Delegates to the standalone play-game Gate (not a model policy) because
        // game play is not scoped to a single Category instance.
        // Nullable ?User in the Gate callback allows guest sessions to pass.
        return Gate::allows('play-game');
    }

    /**
     * Called automatically when authorize() returns false.
     * Returns a JSON error for AJAX callers and a redirect for browser requests.
     */
    protected function failedAuthorization(): void
    {
        $message = 'You must be logged in or continue as a guest to play.';

        if ($this->expectsJson()) {
            throw new HttpResponseException(
                response()->json(['error' => $message, 'status' => 'unauthorized'], 403)
            );
        }

        throw new HttpResponseException(
            redirect()->route('auth.login')->with('error', $message)
        );
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('guess') && is_string($this->input('guess'))) {
            $this->merge(['guess' => trim($this->input('guess'))]);
        }
    }

    public function rules(): array
    {
        return [
            // Empty guess is allowed to trigger hint-reveal logic.
            'guess' => ['nullable', 'string', 'size:1', 'regex:/^[A-Za-z]$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'guess.size' => 'Please enter a single letter',
            'guess.regex' => 'Please enter a single letter',
        ];
    }

    protected function failedValidation(Validator $validator): void
    {
        $message = $validator->errors()->first('guess') ?: 'Please enter a single letter';

        if ($this->expectsJson()) {
            throw new HttpResponseException(response()->json([
                'error' => $message,
                'status' => 'invalid',
            ], 422));
        }

        throw new HttpResponseException(
            redirect()->route('guessing-game.guess')->with('error', $message)
        );
    }
}
