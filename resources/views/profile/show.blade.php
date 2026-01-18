<x-app-layout>
    <div class="max-w-3xl mx-auto mt-10 bg-white shadow rounded p-6 space-y-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h1 class="text-2xl text-gray-900 uppercase font-semibold">Profile</h1>
        </div>

        <!-- Profile Information -->
        <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <span class="text-gray-500 font-medium text-sm">Full Name</span>
                    <span class="text-gray-900 font-semibold">{{ auth()->user()->name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-gray-500 font-medium text-sm">Email Address</span>
                    <span class="text-gray-900 font-semibold">{{ auth()->user()->email }}</span>
                </div>
            </div>
        </div>

        <!-- Change Password Section -->
        <div class="bg-white shadow-sm rounded-lg p-6 space-y-5">
            <h2 class="text-lg font-semibold text-gray-900 border-b border-gray-200 pb-2">Change Password</h2>

            @if(session('success'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-md text-sm">
                    {{ session('success') }}
                </div>
            @endif

            <form method="POST" action="{{ route('profile.password.change') }}" class="space-y-4">
                @csrf

                <!-- Current Password -->
                <div class="flex flex-col">
                    <label class="text-gray-700 font-medium text-sm mb-1">Current Password</label>
                    <input type="password" name="current_password"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                    @error('current_password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- New Password -->
                <div class="relative flex flex-col">
                    <label class="text-gray-700 font-medium text-sm mb-1">New Password</label>
                    <input type="password" name="password" id="password"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                    <span class="absolute inset-y-11 right-3 flex items-center cursor-pointer text-gray-400"
                        onclick="togglePassword('password', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm New Password -->
                <div class="relative flex flex-col">
                    <label class="text-gray-700 font-medium text-sm mb-1">Confirm New Password</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="w-full border border-gray-300 rounded-md px-4 py-2 pr-10 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none"
                        required>
                    <span class="absolute inset-y-11 right-3 flex items-center cursor-pointer text-gray-400"
                        onclick="togglePassword('password_confirmation', this)">
                        <i class="fas fa-eye"></i>
                    </span>
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium text-sm transition">
                        Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function togglePassword(id, el) {
        const input = document.getElementById(id);
        const icon = el.querySelector('i');

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = "password";
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }
    </script>
</x-app-layout>
