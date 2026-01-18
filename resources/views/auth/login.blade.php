<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | IMS</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-br from-gray-100 to-gray-300 flex items-center justify-center">

    <div class="w-full max-w-md bg-white rounded-xl shadow-xl p-8">

        <!-- Logo / Title -->
        <div class="text-center mb-8">
            <div class="flex justify-center mb-3">
                <img src="{{ asset('images/ims_logo.png') }}"
                     alt="IMS Logo"
                     class="h-40 w-auto">
            </div>

            <h1 class="text-2xl font-bold text-gray-800 tracking-wide">
                Intern Management System
            </h1>
        </div>

        <!-- Error Message -->
        @if($errors->any())
            <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-6 text-sm">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Login Form -->
        <form action="{{ route('login.post') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email
                </label>
                <input
                    type="email"
                    name="email"
                    required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           outline-none transition"
                    placeholder="Your Email"
                >
            </div>

            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password
                </label>
                <input
                    type="password"
                    name="password"
                    required
                    class="w-full px-4 py-2.5 border border-gray-300 rounded-lg
                           focus:ring-2 focus:ring-blue-500 focus:border-blue-500
                           outline-none transition"
                    placeholder="••••••••"
                >
            </div>

            <!-- Login Button -->
            <button
                type="submit"
                class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800
                       text-white py-2.5 rounded-lg font-semibold
                       transition duration-200 shadow-md"
            >
                Sign In
            </button>
        </form>

        <!-- Footer -->
        <div class="text-center text-xs text-gray-400 mt-8">
            {{ date('Y') }} IMS
        </div>

    </div>

</body>
</html>
