<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
    <style>
        body {
            background-color: #DFDBE5;
            background-size: cover;
            background-repeat: no-repeat;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div x-data="loginForm()" class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full opacity-80">
        <h1 class="text-2xl font-bold text-gray-700 mb-6 text-center">Login</h1>
        
        <div x-show="errors.length" class="bg-red-50 text-red-600 p-4 rounded mb-6">
            <ul class="list-disc pl-5">
                <template x-for="(error, index) in errors" :key="index">
                    <li x-text="error"></li>
                </template>
            </ul>
        </div>

        <form @submit.prevent="submitForm">
            @csrf
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" name="email" x-model="form.email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#a49cb1] focus:border-transparent" 
                    required>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" name="password" x-model="form.password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#a49cb1] focus:border-transparent" 
                    required>
            </div>
            
            <button type="submit" 
                class="w-full bg-[#9f84c7] hover:bg-[#a49cb1] text-white font-medium py-2 px-4 rounded-md transition duration-300">
                Login
            </button>
        </form>
        
        <div class="mt-4 text-center">
            <p class="text-gray-700">Don't have an account? <a href="register" class="text-[#9f84c7] hover:text-[#a49cb1]">Create one</a></p>
        </div>
    </div>

   
</body>
</html>
