<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>SSO Login - {{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased">
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
            <div class="mb-4">
                <h2 class="text-2xl font-bold text-center text-gray-800">
                    Single Sign-On
                </h2>
                <p class="text-center text-gray-600 mt-2">
                    Choose a partner application to continue
                </p>
            </div>

            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="space-y-4">
                @auth
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">
                            Logged in as: <strong>{{ auth()->user()->name }}</strong>
                        </p>
                        <p class="text-sm text-gray-500 mb-4">
                            Click below to access partner applications
                        </p>
                    </div>

                    @foreach($partners as $partner)
                        <a href="{{ route('sso.redirect', $partner->identifier) }}"
                           class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Continue to {{ $partner->name }}
                        </a>
                    @endforeach

                    <div class="mt-4 text-center">
                        <a href="{{ url($returnUrl) }}" class="text-sm text-blue-600 hover:text-blue-500">
                            ← Back to application
                        </a>
                    </div>
                @else
                    <div class="text-center">
                        <p class="text-gray-600 mb-4">
                            You need to be logged in to use Single Sign-On
                        </p>
                        <a href="{{ route('login') }}?return_url={{ urlencode(request()->fullUrl()) }}"
                           class="w-full flex justify-center py-3 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                            Log in to continue
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>
</body>
</html>
