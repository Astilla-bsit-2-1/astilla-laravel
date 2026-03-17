<?php

namespace App\Http\Requests;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Gate;

class StartGameRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Delegates to CategoryPolicy::start(User $user, Category $category).
        // Non-nullable User type-hint auto-denies guests before the policy runs.
        return Gate::allows('start', Category::class);
    }

    /**
     * Called automatically when authorize() returns false.
     * Redirects browsers back to the landing page with a flash message instead
     * of throwing a bare HTTP 403.
     */
    protected function failedAuthorization(): void
    {
        throw new HttpResponseException(
            redirect()->route('guessing-game.select')
                ->with('error', 'Guest mode is restricted to random games only. Please log in or register to choose a category.')
        );
    }

    public function validationData(): array
    {
        return array_merge($this->all(), [
            'category' => $this->route('category'),
        ]);
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:80', 'regex:/^[a-zA-Z_]+$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'category.regex' => 'Invalid category',
        ];
    }
}
