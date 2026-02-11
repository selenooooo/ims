<x-app-layout>
    <div class="max-w-3xl mx-auto mt-10 bg-white shadow rounded p-6 space-y-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h1 class="text-2xl text-gray-900 uppercase font-semibold">Profile</h1>

            @php
                $message = session('success') ?? session('warning');
                $bgColor = session('success') ? 'bg-green-500 hover:bg-green-600' : 'bg-red-500 hover:bg-red-600';
            @endphp

            @if($message)
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)" x-transition class="fixed top-5 inset-x-0 flex justify-center z-50">
                    <div class="{{ $bgColor }} text-white px-6 py-4 rounded shadow-lg flex items-center space-x-3">
                        <span>{{ $message }}</span>
                        <button type="button" @click="show = false" class="ml-auto text-white font-bold px-2 py-1 rounded">&times;</button>
                    </div>
                </div>
            @endif

        </div>

        <!-- Profile Information -->
        <div class="bg-white shadow-sm rounded-lg p-6 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div class="flex flex-col">
                    <span class="text-gray-500 font-medium text-sm">Name</span>
                    <span class="text-gray-900 font-semibold">{{ auth()->user()->name }}</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-gray-500 font-medium text-sm">Email B2BE</span>
                    <span class="text-gray-900 font-semibold">{{ auth()->user()->email }}</span>
                </div>
            </div>

            <!-- Extra info for interns -->
            @if(auth()->user()->role === 'intern' && $intern)
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div class="flex flex-col">
                            <span class="text-gray-500 font-medium text-sm">Employee ID</span>
                            <span class="text-gray-900 font-semibold">{{ $intern->employee_id }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-500 font-medium text-sm">Report Date</span>
                            <span class="text-gray-900 font-semibold">{{ $intern->report_date }}</span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-500 font-medium text-sm">Duration</span>
                            <span class="text-gray-900 font-semibold">{{ $intern->intern_duration }} month(s) </span>
                        </div>
                        <div class="flex flex-col">
                            <span class="text-gray-500 font-medium text-sm">End Date</span>
                            <span class="text-gray-900 font-semibold">{{ $intern->end_date }} </span>
                        </div>
                    </div>
            @endif
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
                    <button type="submit" onclick="disableSubmit(this)" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-medium text-sm transition">
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

    function disableSubmit(button) {
        const form = button.form;

        if (!form.checkValidity()) {
            form.reportValidity(); // show validation errors
            return;
        }

        button.disabled = true;
        button.innerText = "Updating...";
        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        button.classList.add('bg-gray-400', 'cursor-not-allowed');
        button.form.submit();
    }
    </script>
</x-app-layout>
