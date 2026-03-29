<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - KibouFarm</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            500: '#0ea5e9',
                            600: '#0284c7',
                        }
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50 flex items-center justify-center h-screen">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8 border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-bold text-brand-600 mb-2">KibouFarm</h1>
            <p class="text-gray-500">Silakan login untuk masuk ke dashboard</p>
        </div>

        <form action="{{ route('login.post') }}" method="POST">
            @csrf
            
            <div class="mb-5">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                <input type="email" name="email" id="email" 
                    class="w-full px-4 py-2 border rounded-lg focus:ring-brand-500 focus:border-brand-500 @error('email') border-red-500 @else border-gray-300 @enderror" 
                    placeholder="admin@gmail.com" required autofocus value="{{ old('email') }}">
                @error('email')
                    <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-6">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="password" id="password" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-brand-500 focus:border-brand-500" 
                    placeholder="••••••••" required>
            </div>

            <button type="submit" 
                class="w-full bg-brand-600 text-white font-semibold py-2.5 rounded-lg hover:bg-brand-500 transition duration-200">
                Sign In
            </button>
        </form>
    </div>
</body>
</html>
