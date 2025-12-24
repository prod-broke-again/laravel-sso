# Installation Guide

## Prerequisites

- Laravel 9.0 or higher
- PHP 8.1 or higher
- Composer

## Step 1: Install Package

```bash
composer require packages/laravel-sso
```

## Step 2: Publish Configuration

```bash
php artisan vendor:publish --tag=sso-config
```

This will create `config/sso.php` file.

## Step 3: Publish Migrations

```bash
php artisan vendor:publish --tag=sso-migrations
```

## Step 4: Run Migrations

```bash
php artisan migrate
```

## Step 5: Configure Partners

Edit `config/sso.php` and add your partner applications:

```php
'partners' => [
    'partner1' => [
        'name' => 'My Partner App',
        'url' => 'https://partner.example.com',
        'enabled' => true,
    ],
],
```

## Step 6: Configure Environment

Add to your `.env` file:

```env
SSO_APP_IDENTIFIER=app1
SSO_TOKEN_LIFETIME=5
SSO_REDIRECT_AFTER_LOGIN=/dashboard
```

## Step 7: Configure Partners in Both Applications

Make sure both applications have each other configured as partners.

### Application 1 (.env)
```env
SSO_APP_IDENTIFIER=app1
```

### Application 2 (.env)
```env
SSO_APP_IDENTIFIER=app2
```

### Application 1 (config/sso.php)
```php
'partners' => [
    'app2' => [
        'name' => 'Application 2',
        'url' => 'https://app2.example.com',
        'enabled' => true,
    ],
],
```

### Application 2 (config/sso.php)
```php
'partners' => [
    'app1' => [
        'name' => 'Application 1',
        'url' => 'https://app1.example.com',
        'enabled' => true,
    ],
],
```

## Verification

1. Visit `/sso/login` in your application
2. You should see SSO options for configured partners
3. Try logging in and using SSO between applications

## Troubleshooting

- Ensure both applications are accessible via HTTPS in production
- Check that partner URLs are correct
- Verify that both applications have unique `SSO_APP_IDENTIFIER` values
- Check Laravel logs for any errors
