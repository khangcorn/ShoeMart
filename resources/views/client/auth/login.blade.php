<form method="POST" action="{{ route('login') }}">
    @csrf

    <div class="flex items-center justify-center min-h-screen bg-gray-50 px-4">
        <div class="bg-white p-4 rounded-xl shadow-md w-full max-w-4xl grid grid-cols-1 md:grid-cols-2 gap-4">

            <!-- Image -->
            <div class="hidden md:block">
                <img
                    class="rounded-l-lg w-full h-full object-cover"
                    src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_586,c_limit/a4aef648-8791-47d0-a667-3e2fde99f3b6/nike-just-do-it.jpg"
                    alt="Nike Just Do It"
                />
            </div>

            <!-- Login form -->
            <div class="flex flex-col items-center justify-center w-full mx-auto">
                <h2 class="text-3xl font-semibold text-center text-gray-800 mb-4">Login</h2>
                <p class="text-sm text-center text-gray-600 mb-6">Please enter your credentials to access your account.</p>
@if($errors->any())
    <div id="error-alert" class="text-red-500 text-sm mt-2 transition-opacity duration-1000">
        @foreach($errors->all() as $error)
            <div>{{ $error }}</div>
        @endforeach
    </div>
    <script>
    setTimeout(() => {
        const alert = document.getElementById('error-alert');
        if (alert) {
            alert.classList.add('opacity-0');
            setTimeout(() => alert.remove(), 1000); // remove khỏi DOM sau khi mờ dần
        }
    }, 5000);
</script>

@endif


                <div class="space-y-6 w-full max-w-md">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            placeholder="Enter your email"
                            class="w-full px-4 py-3 border rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none mt-2"
                            required
                        />
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            class="w-full px-4 py-3 border rounded-md border-gray-300 focus:ring-2 focus:ring-blue-500 focus:outline-none mt-2"
                            required
                        />
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        class="w-full py-3 bg-black rounded-full text-white font-medium hover:bg-gray-700 focus:outline-none transition duration-200 mt-4"
                    >
                        Log In
                    </button>
                </div>

                <!-- Footer -->
                <div class="mt-4 text-center text-sm text-gray-600">
                    <a href="/register" class="text-blue-600 hover:underline">Sign Up</a> |
                    <a href="/forgot-password" class="text-blue-600 hover:underline">Forgot Password?</a>
                </div>
            </div>

        </div>
    </div>
</form>


<script>
    function loginUser() {
        const email = document.getElementById("email").value;
        const password = document.getElementById("password").value;
        console.log("Email:", email, "Password:", password);
        // TODO: Send login data to server
    }
</script>

<script src="https://cdn.tailwindcss.com"></script>
