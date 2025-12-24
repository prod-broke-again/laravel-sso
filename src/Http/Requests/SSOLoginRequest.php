<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * SSO Login Request
 *
 * Validates SSO login page requests.
 */
class SSOLoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool Always true for SSO login page
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
            'partner' => ['nullable', 'string', 'max:255'],
            'return_url' => ['nullable', 'url', 'max:2048'],
        ];
    }

    /**
     * Get the partner identifier.
     *
     * @return string|null Partner identifier or null
     */
    public function getPartner(): ?string
    {
        return $this->input('partner');
    }

    /**
     * Get the return URL.
     *
     * @return string Return URL with fallback
     */
    public function getReturnUrl(): string
    {
        return $this->input('return_url', '/');
    }
}
