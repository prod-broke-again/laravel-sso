<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SSO Callback Request
 *
 * Validates SSO callback requests from partner applications.
 */
class SSOCallbackRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always true for SSO callbacks
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'token' => ['required', 'string', 'min:64', 'max:64'],
            'app' => ['required', 'string', 'max:255'],
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
            'token.required' => 'SSO token is required.',
            'token.min' => 'SSO token format is invalid.',
            'token.max' => 'SSO token format is invalid.',
            'app.required' => 'Source application identifier is required.',
            'return_url.url' => 'Return URL must be a valid URL.',
        ];
    }

    /**
     * Get the SSO token from the request.
     *
     * @return string The SSO token
     */
    public function getToken(): string
    {
        return $this->input('token');
    }

    /**
     * Get the source application identifier.
     *
     * @return string Source app identifier
     */
    public function getSourceApp(): string
    {
        return $this->input('app');
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
