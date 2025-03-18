<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Helpdesk Kota</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@3.4/dist/tailwind.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100 dark:bg-gray-900 flex items-center justify-center h-screen">
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6 w-full max-w-md">
        @if (session('success'))
            <div class="bg-green-100 text-green-800 p-4 mb-4 rounded dark:bg-green-900 dark:text-green-200">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-100 text-red-800 p-4 mb-4 rounded dark:bg-red-900 dark:text-red-200">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <h2 class="text-2xl font-bold mb-4 text-gray-800 dark:text-gray-200">Login</h2>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="mb-4">
                <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Username</label>
                <input type="text" id="username" name="username" value="{{ old('username') }}" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200" required autocomplete="username">
                @error('username')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @endif
            </div>

            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Password</label>
                <input type="password" id="password" name="password" class="mt-1 block w-full border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 dark:focus:border-indigo-600 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-200" required autocomplete="current-password">
                @error('password')
                    <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @endif
            </div>

            <div class="mb-4">
                <label for="remember_me" class="inline-flex items-center">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:focus:ring-offset-gray-800">
                    <span class="ms-2 text-sm text-gray-600 dark:text-gray-400">Remember me</span>
                </label>
            </div>

            <div class="flex items-center justify-end">
                <a href="{{ route('password.request') }}" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">Forgot your password?</a>
                <button type="submit" class="ms-3 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
