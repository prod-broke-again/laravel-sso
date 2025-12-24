<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SSO Redirect Request
 *
 * Validates SSO redirect requests to partner applications.
 */
class SSORedirectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool True if user is authenticated
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'return_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'return_url.url' => 'Return URL must be a valid URL.',
        ];
    }

    /**
     * Get the return URL.
     *
     * @return string|null Return URL or null
     */
    public function getReturnUrl(): ?string
    {
        return $this->input('return_url');
    }
}
