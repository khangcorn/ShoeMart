<script src="https://cdn.tailwindcss.com"></script>

@if(session('success'))
    <div class="bg-green-500 text-white p-4 rounded-md mb-4">
        {{ session('success') }}
    </div>
@endif

@if ($errors->any())
    <div class="bg-red-500 text-white p-4 rounded-md mb-4">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="flex justify-center items-center min-h-screen bg-gray-50">
    <div class="bg-white p-4 shadow-lg rounded-b-lg  w-full max-w-xl space-y-6 mt-4 mb-4">

        <!-- Image section -->
        <div class="hidden md:block mb-0">
            <img
                class=" w-full h-60 object-cover rounded-t-lg"
                src="https://static.nike.com/a/images/f_auto/dpr_1.0,cs_srgb/h_586,c_limit/87705d10-83d8-4da3-b091-10fc8d62a45e/nike-just-do-it.png"
                alt="Nike Just Do It"
            />
        </div>

        <!-- Registration form section -->
        <div class="flex flex-col items-center justify-center ">
            <h2 class="text-3xl font-semibold text-gray-800 mb-2">Create an Account</h2>
            <p class="text-sm text-gray-600 ">Join us and enjoy great deals!</p>

            <!-- Form -->
            <form action="{{ route('register') }}" method="POST" class="w-full space-y-6">
                @csrf

                <!-- Username -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="username" value="{{ old('username') }}" placeholder="Enter your username">
                    @error('username')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                    <input type="email" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="email" value="{{ old('email') }}" placeholder="Enter your email">
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-700">Phone Number</label>
                    <input type="text" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="phone" value="{{ old('phone') }}" placeholder="Enter your phone number">
                    @error('phone')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input type="password" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="password" placeholder="Enter your password">
                    @error('password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                    <input type="password" class="w-full px-4 py-3 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mt-2" name="password_confirmation" placeholder="Confirm your password">
                    @error('password_confirmation')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" class="w-full py-3 bg-black text-white rounded-full hover:bg-gray-700 focus:outline-none transition duration-200">
                    Sign Up
                </button>
            </form>

            <!-- Login link -->
            <div class="text-center mt-2 mb-4">
                <a href="{{ route('login') }}" class="text-blue-600 hover:underline">Already have an account? Log in</a>
            </div>
        </div>
    </div>
</div>
