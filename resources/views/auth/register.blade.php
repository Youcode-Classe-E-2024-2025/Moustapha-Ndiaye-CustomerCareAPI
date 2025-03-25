<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.js" defer></script>
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
    <div class="bg-white p-8 rounded-lg shadow-xl max-w-md w-full opacity-80" x-data="{
        name: '', email: '', password: '', password_confirmation: '', errors: []
    }">
        <h1 class="text-2xl font-bold text-gray-700 mb-6 text-center">Create Account</h1>

        <!-- Error Display -->
        <template x-if="errors.length > 0">
            <div class="bg-red-50 text-red-600 p-4 rounded mb-6">
                <ul class="list-disc pl-5">
                    <template x-for="(error, index) in errors" :key="index">
                        <li x-text="error"></li>
                    </template>
                </ul>
            </div>
        </template>

        <!-- Form -->
        <form @submit.prevent="submitForm">
            <div class="mb-4">
                <label for="name" class="block text-gray-700 font-medium mb-2">Name</label>
                <input type="text" id="name" x-model="name" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#a49cb1] focus:border-transparent" 
                    required>
            </div>
            
            <div class="mb-4">
                <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                <input type="email" id="email" x-model="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#a49cb1] focus:border-transparent" 
                    required>
            </div>
            
            <div class="mb-4">
                <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                <input type="password" id="password" x-model="password" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#a49cb1] focus:border-transparent" 
                    required>
            </div>
            
            <div class="mb-6">
                <label for="password_confirmation" class="block text-gray-700 font-medium mb-2">Confirm Password</label>
                <input type="password" id="password_confirmation" x-model="password_confirmation" 
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-[#a49cb1] focus:border-transparent" 
                    required>
            </div>
            
            <button type="submit" 
                class="w-full bg-[#9f84c7] hover:bg-[#a49cb1] text-white font-medium py-2 px-4 rounded-md transition duration-300">
                Create Account
            </button>

            <div class="mt-4 text-center">
                <p class="text-gray-700">Already registered? <a href="login" class="text-[#9f84c7] hover:text-[#a49cb1]">Login</a></p>
            </div>
        </form>
    </div>

    <script>
        function submitForm() {
            // Reset errors before submitting
            this.errors = [];

            const formData = new FormData();
            formData.append('name', this.name);
            formData.append('email', this.email);
            formData.append('password', this.password);
            formData.append('password_confirmation', this.password_confirmation);
            formData.append('_token', '{{ csrf_token() }}');

            fetch('{{ url("registrationUser") }}', {
                method: 'POST',
                body: formData,
            })
            .then(response => response.json())
            .then(data => {
                if (data.errors) {
                    this.errors = Object.values(data.errors).flat();
                } else {
                    window.location.href = 'login';  // Redirect to login page
                }
            })
            .catch(error => {
                console.error('Error:', error);
                this.errors.push("Something went wrong, please try again.");
            });
        }
    </script>
</body>
</html>
