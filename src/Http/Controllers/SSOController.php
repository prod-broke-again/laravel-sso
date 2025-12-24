<?php

declare(strict_types=1);

namespace Packages\LaravelSSO\Http\Controllers;

use Packages\LaravelSSO\Contracts\SSOServiceInterface;
use Packages\LaravelSSO\Contracts\PartnerServiceInterface;
use Packages\LaravelSSO\Http\Requests\SSOLoginRequest;
use Packages\LaravelSSO\Http\Requests\SSOCallbackRequest;
use Packages\LaravelSSO\Http\Requests\SSORedirectRequest;
use Packages\LaravelSSO\Exceptions\SsoException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

/**
 * SSO Controller
 *
 * Handles SSO authentication requests and callbacks.
 */
readonly class SSOController
{
    public function __construct(
        private SSOServiceInterface $ssoService,
        private PartnerServiceInterface $partnerService,
    ) {}

    /**
     * Show SSO login page with partner options.
     *
     * @param SSOLoginRequest $request Validated request
     * @return View SSO login page
     */
    public function login(SSOLoginRequest $request): View
    {
        $partners = $this->partnerService->getEnabledPartners();

        return view('laravel-sso::login', [
            'partners' => $partners,
            'returnUrl' => $request->getReturnUrl(),
        ]);
    }

    /**
     * Redirect to partner login.
     *
     * @param SSORedirectRequest $request Validated request
     * @param string $partnerIdentifier Partner identifier
     * @return RedirectResponse Redirect to partner or back with error
     */
    public function redirectToPartner(SSORedirectRequest $request, string $partnerIdentifier): RedirectResponse
    {
        try {
            $user = auth()->user();
            $loginUrlDto = $this->ssoService->buildLoginUrl($partnerIdentifier, $user);

            Log::info('SSO redirect successful', [
                'user_id' => $user->id,
                'partner' => $partnerIdentifier,
                'url' => $loginUrlDto->url,
            ]);

            return redirect($loginUrlDto->url);

        } catch (SsoException $e) {
            Log::warning('SSO redirect failed', [
                'partner' => $partnerIdentifier,
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to redirect to partner: ' . $e->getMessage());

        } catch (\Throwable $e) {
            Log::error('Unexpected SSO redirect error', [
                'partner' => $partnerIdentifier,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect()->back()
                ->with('error', 'An unexpected error occurred. Please try again.');
        }
    }

    /**
     * Handle SSO callback from partner.
     *
     * @param SSOCallbackRequest $request Validated request
     * @return RedirectResponse Redirect after authentication
     */
    public function callback(SSOCallbackRequest $request): RedirectResponse
    {
        $token = $request->getToken();
        $sourceApp = $request->getSourceApp();
        $returnUrl = $request->getReturnUrl() ?? config('sso.redirect_after_login');

        try {
            $tokenData = $this->ssoService->validateToken($token);
            $user = $this->ssoService->authenticateFromTokenData($tokenData);

            if (!$user) {
                Log::error('SSO authentication failed: user creation failed', [
                    'token_data' => $tokenData,
                ]);

                return redirect('/login')
                    ->with('error', 'Authentication failed: Unable to create or find user account.');
            }

            Log::info('SSO authentication successful', [
                'user_id' => $user->id,
                'source_app' => $sourceApp,
                'partner_token' => $token,
            ]);

            return redirect($returnUrl)
                ->with('success', 'Successfully logged in via SSO from ' . $sourceApp . '.');

        } catch (SsoException $e) {
            Log::warning('SSO callback validation failed', [
                'token' => $token,
                'source_app' => $sourceApp,
                'error' => $e->getMessage(),
                'context' => $e->getContext(),
            ]);

            return redirect('/login')
                ->with('error', 'SSO authentication failed: ' . $e->getMessage());

        } catch (\Throwable $e) {
            Log::error('Unexpected SSO callback error', [
                'token' => $token,
                'source_app' => $sourceApp,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return redirect('/login')
                ->with('error', 'An unexpected error occurred during authentication.');
        }
    }
}
