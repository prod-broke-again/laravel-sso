# Laravel SSO Package

A Laravel package that enables Single Sign-On (SSO) between multiple Laravel applications. Users can seamlessly authenticate across partner applications with a single login.

## Features

- 🔐 Cross-application authentication
- 🛡️ Secure token-based SSO
- 🎨 Ready-to-use Blade components
- ⚙️ Configurable partner applications
- 🧹 Automatic token cleanup
- 📱 Mobile-friendly interface

## Requirements

- Laravel 9.0 or higher
- PHP 8.1 or higher

## Installation

1. Install the package via Composer:

```bash
composer require packages/laravel-sso
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --tag=sso-config
```

3. Publish and run the migrations:

```bash
php artisan vendor:publish --tag=sso-migrations
php artisan migrate
```

4. Configure your partner applications in `config/sso.php`

## Configuration

### Basic Setup

Edit `config/sso.php` to configure your SSO setup:

```php
return [
    // Your application identifier (must be unique across partners)
    'app_identifier' => env('SSO_APP_IDENTIFIER', 'app1'),

    // Partner applications
    'partners' => [
        'partner1' => [
            'name' => 'Partner Application 1',
            'url' => 'https://partner1.example.com',
            'enabled' => env('SSO_PARTNER1_ENABLED', true),
        ],
        'partner2' => [
            'name' => 'Partner Application 2',
            'url' => 'https://partner2.example.com',
            'enabled' => env('SSO_PARTNER2_ENABLED', true),
        ],
    ],

    // Token lifetime in minutes
    'token_lifetime' => env('SSO_TOKEN_LIFETIME', 5),

    // Redirect after successful SSO login
    'redirect_after_login' => env('SSO_REDIRECT_AFTER_LOGIN', '/dashboard'),
];
```

### Environment Variables

Add these to your `.env` file:

```env
# SSO Configuration
SSO_APP_IDENTIFIER=app1
SSO_TOKEN_LIFETIME=5
SSO_REDIRECT_AFTER_LOGIN=/dashboard

# Partner Applications
SSO_PARTNER1_ENABLED=true
SSO_PARTNER2_ENABLED=true
```

## Usage

### 1. Adding SSO Buttons

Use the Blade component to add SSO login buttons:

```blade
{{-- Using partner identifier --}}
<x-sso-button partner="partner1" />

{{-- Using partner model --}}
<x-sso-button :partner="$partner" />

{{-- Custom styling --}}
<x-sso-button partner="partner1" class="custom-button-class" />

{{-- Custom text --}}
<x-sso-button partner="partner1">
    Login to Partner App
</x-sso-button>
```

### 2. Manual SSO URL Generation

Use the SSO service to generate login URLs:

```php
use Packages\LaravelSSO\Services\SSOService;

$ssoService = app(SSOService::class);

// Generate SSO URL for a user
$url = $ssoService->buildLoginUrl('partner1', auth()->user());

// Redirect to partner
return redirect($url);
```

### 3. Programmatic Token Generation

```php
use Packages\LaravelSSO\Services\SSOService;

$ssoService = app(SSOService::class);

// Generate token for current user
$token = $ssoService->generateToken(auth()->user(), 'partner1');

// Get token URL
$url = "https://partner1.example.com/sso/callback?token={$token->token}&app=" . config('sso.app_identifier');
```

## Routes

The package registers these routes (prefix configurable):

- `GET /sso/login` - SSO login page
- `GET /sso/redirect/{partner}` - Redirect to partner (requires auth)
- `GET /sso/callback` - Handle SSO callback

## Workflow

1. **User Authentication**: User logs into Application A
2. **SSO Request**: User clicks "Login to Partner B" button
3. **Token Generation**: Application A generates secure SSO token
4. **Redirect**: User is redirected to Application B with token
5. **Token Validation**: Application B validates token and authenticates user
6. **Auto Login**: User is automatically logged into Application B

## Security Features

- **Token Expiration**: Tokens expire after configurable time (default: 5 minutes)
- **Single Use**: Each token can only be used once
- **Encrypted Data**: User data is encrypted in tokens
- **IP/User Agent Tracking**: Optional security logging
- **Partner Validation**: Only configured partners are allowed

## Database Tables

### sso_partners
Stores partner application configurations.

### sso_tokens
Stores SSO authentication tokens with expiration and usage tracking.

## Commands

### Cleanup Expired Tokens

Run this command periodically to clean up expired tokens:

```bash
php artisan sso:cleanup
```

You can also schedule it in `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('sso:cleanup')->daily();
}
```

## Advanced Configuration

### Custom User Model

If you're using a custom user model, update the configuration:

```php
'user_model' => 'App\Models\CustomUser',
```

### Custom Routes

You can disable auto-loading of routes and define them manually:

```php
// In your service provider
$this->app->register(\Packages\LaravelSSO\Providers\SSOServiceProvider::class);

// Manually load routes where needed
Route::middleware(['web'])->group(function () {
    // Your custom SSO routes
});
```

### Custom Blade Components

Publish the views to customize the UI:

```bash
php artisan vendor:publish --tag=sso-views
```

## Troubleshooting

### Common Issues

1. **"Partner not found" error**
   - Check that the partner is configured in `config/sso.php`
   - Ensure the partner is enabled

2. **"Invalid SSO token" error**
   - Check token expiration time
   - Verify token hasn't been used already
   - Check that the partner URLs are correct

3. **Redirect loops**
   - Ensure `SSO_REDIRECT_AFTER_LOGIN` is set correctly
   - Check that callback URLs are properly configured

### Debug Mode

Enable Laravel debug mode to see detailed error messages:

```env
APP_DEBUG=true
```

Check Laravel logs for SSO-related errors:

```bash
tail -f storage/logs/laravel.log | grep -i sso
```

## Testing

The package includes comprehensive tests. To run them:

```bash
composer test
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests
5. Submit a pull request

## License

This package is open-sourced software licensed under the [MIT license](LICENSE).

## Support

If you need help, please:

1. Check the documentation
2. Search existing issues
3. Create a new issue with detailed information

---

**Note**: This package requires careful configuration of partner applications and shared secrets. Always use HTTPS in production and keep your SSO tokens secure.
