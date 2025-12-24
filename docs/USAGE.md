# Usage Guide

## Basic Usage

### Adding SSO Buttons

Add SSO login buttons to your views:

```blade
{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Login</div>

                <div class="card-body">
                    {{-- Regular login form --}}
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        {{-- Your login form fields --}}
                    </form>

                    {{-- SSO Options --}}
                    <div class="mt-4">
                        <h5 class="text-center mb-3">Or login with partner:</h5>
                        <div class="text-center">
                            <x-sso-button partner="partner1" class="btn btn-primary me-2" />
                            <x-sso-button partner="partner2" class="btn btn-secondary" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
```

### SSO Login Page

Users can visit `/sso/login` to see all available SSO options.

### Programmatic Usage

```php
<?php

namespace App\Http\Controllers;

use Packages\LaravelSSO\Services\SSOService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function redirectToPartner(Request $request, SSOService $ssoService)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $partnerIdentifier = $request->get('partner');
        $url = $ssoService->buildLoginUrl($partnerIdentifier, auth()->user());

        return redirect($url);
    }
}
```

## Advanced Usage

### Custom User Data

Pass additional user data during SSO:

```php
$token = $ssoService->generateToken(auth()->user(), 'partner1', [
    'role' => auth()->user()->role,
    'permissions' => auth()->user()->permissions,
]);
```

### Custom Redirect URLs

```php
// In your login form
<a href="{{ route('sso.redirect', ['partner' => 'partner1']) }}?return_url={{ urlencode('/custom/redirect') }}">
    Login with Partner
</a>
```

### API Integration

```php
<?php

namespace App\Http\Controllers\Api;

use Packages\LaravelSSO\Services\SSOService;
use Illuminate\Http\Request;

class SSOApiController extends Controller
{
    public function getSSOUrl(Request $request, SSOService $ssoService)
    {
        $request->validate([
            'partner' => 'required|string',
            'return_url' => 'nullable|url',
        ]);

        $partner = $request->get('partner');
        $url = $ssoService->buildLoginUrl($partner, auth()->user());

        if ($request->has('return_url')) {
            $url .= '&return_url=' . urlencode($request->get('return_url'));
        }

        return response()->json([
            'sso_url' => $url,
        ]);
    }
}
```

## Frontend Integration

### JavaScript/AJAX

```javascript
// Get SSO URL via AJAX
fetch('/api/sso/url?partner=partner1', {
    headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    }
})
.then(response => response.json())
.then(data => {
    window.location.href = data.sso_url;
});
```

### Vue.js Component

```vue
<template>
  <div>
    <button @click="loginWithPartner('partner1')" class="btn btn-primary">
      Login with Partner
    </button>
  </div>
</template>

<script>
export default {
  methods: {
    async loginWithPartner(partner) {
      try {
        const response = await fetch(`/api/sso/url?partner=${partner}`);
        const data = await response.json();
        window.location.href = data.sso_url;
      } catch (error) {
        console.error('SSO login failed:', error);
      }
    }
  }
}
</script>
```

## Security Considerations

### Token Security

- Tokens are encrypted and signed
- Tokens expire after 5 minutes (configurable)
- Each token can only be used once
- User data is encrypted in the database

### Best Practices

1. **Always use HTTPS** in production
2. **Validate partner URLs** carefully
3. **Monitor SSO logs** for suspicious activity
4. **Keep token lifetime short** (1-5 minutes recommended)
5. **Use unique app identifiers** across all applications

### Security Headers

Consider adding security headers for SSO endpoints:

```php
// In your middleware
header('X-Frame-Options: DENY');
header('X-Content-Type-Options: nosniff');
header('Referrer-Policy: strict-origin-when-cross-origin');
```

## Customization

### Custom Views

Publish and customize the SSO views:

```bash
php artisan vendor:publish --tag=sso-views
```

### Custom Components

Create your own SSO button component:

```php
<?php

namespace App\View\Components;

use Illuminate\View\Component;

class CustomSsoButton extends Component
{
    public $partner;

    public function __construct($partner)
    {
        $this->partner = $partner;
    }

    public function render()
    {
        return view('components.custom-sso-button');
    }
}
```

### Custom Service

Extend the SSO service for custom logic:

```php
<?php

namespace App\Services;

use Packages\LaravelSSO\Services\SSOService as BaseSSOService;

class CustomSSOService extends BaseSSOService
{
    public function buildLoginUrl(string $partnerIdentifier, $user): string
    {
        // Custom logic here
        $url = parent::buildLoginUrl($partnerIdentifier, $user);

        // Add custom parameters
        return $url . '&custom_param=value';
    }
}
```

Register your custom service in a service provider:

```php
$this->app->singleton(\Packages\LaravelSSO\Services\SSOService::class, \App\Services\CustomSSOService::class);
```
