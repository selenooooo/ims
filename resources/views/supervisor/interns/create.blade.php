<x-app-layout>
    <div class="max-w-9xl mx-auto px-4 space-y-6">
        <!-- Header -->
        <div class="bg-white border-b border-gray-200 p-6">
            <div class="flex items-center gap-4">
                <div class="bg-blue-100 p-3 rounded-lg">
                    <i class="fas fa-user-plus text-blue-600 text-xl"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Intern's Registration</h2>
                </div>
            </div>
        </div>

        <!-- Form Card -->
        <div class="bg-white shadow-md border border-gray-200 rounded-lg p-8">
            <form method="POST" action="{{ route('supervisor.interns.store') }}" class="space-y-6">
                @csrf

                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Employee ID -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-id-badge text-blue-600 mr-2"></i>
                            Employee ID
                        </label>
                        <div class="relative">
                            <input type="text" name="employee_id" 
                                id="employee_id"
                                class="w-full pl-3 pr-4 py-3 border border-gray-400 rounded-md 
                                        focus:border-blue-500 focus:ring-2 focus:ring-blue-100 
                                        hover:border-gray-500 transition-colors" 
                                maxlength="10"
                                pattern="\d*"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);"
                                required>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-user text-blue-600 mr-2"></i>
                            Full Name
                        </label>
                        <div class="relative">
                            <input type="text" name="name" 
                                   class="w-full pl-2 pr-4 py-3 border border-gray-400 rounded-md 
                                          focus:border-blue-500 focus:ring-2 focus:ring-blue-100 
                                          hover:border-gray-500 transition-colors" 
                                   placeholder="Intern's Name" required>
                        </div>
                    </div>

                    <!-- Email -->
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-envelope text-blue-600 mr-2"></i>
                            Email Address
                        </label>
                        <div class="relative">
                            <input type="email" name="email" 
                                   class="w-full pl-2 pr-4 py-3 border border-gray-400 rounded-md 
                                          focus:border-blue-500 focus:ring-2 focus:ring-blue-100 
                                          hover:border-gray-500 transition-colors" 
                                   placeholder="intern@b2be.com" required>
                        </div>
                    </div>

                    <!-- Report Date -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                            Report Date
                        </label>
                        <div class="relative">
                            <input type="date" name="report_date" 
                                   class="w-full pl-2 pr-4 py-3 border border-gray-400 rounded-md 
                                          focus:border-blue-500 focus:ring-2 focus:ring-blue-100 
                                          hover:border-gray-500 transition-colors
                                          cursor-pointer" required>
                        </div>
                    </div>

                    <!-- Intern Duration -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-clock text-blue-600 mr-2"></i>
                            Duration (Months)
                        </label>
                        <div class="relative">
                            <select name="intern_duration" 
                                    class="w-full pl-2 pr-10 py-3 border border-gray-400 rounded-md 
                                           focus:border-blue-500 focus:ring-2 focus:ring-blue-100 
                                           hover:border-gray-500 transition-colors
                                           appearance-none cursor-pointer" required>
                                <option value="" disabled selected>Select duration</option>
                                @for ($i = 1; $i <= 12; $i++)
                                    <option value="{{ $i }}">{{ $i }} {{ $i   ? 'month' : 'months' }}</option>
                                @endfor
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <i class="fas fa-chevron-down text-gray-500"></i>
                            </div>
                        </div>
                    </div>

                    <!-- End Date -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-calendar-check text-blue-600 mr-2"></i>
                            End Date
                        </label>
                        <div class="relative">
                            <input type="date" id="end_date" name="end_date"
                                   class="w-full pl-2 pr-4 py-3 border border-gray-400 rounded-md 
                                          focus:border-blue-500 focus:ring-2 focus:ring-blue-100 
                                          hover:border-gray-500 transition-colors
                                          cursor-pointer bg-gray-50">
                        </div>
                        <!-- <p class="text-xs text-gray-500">Calculated automatically based on report date and duration</p> -->
                    </div>

                    <!-- Password -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-lock text-blue-600 mr-2"></i>
                            Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-500"></i>
                            </div>
                            <input type="password" name="password" id="password"
                                class="w-full pl-10 pr-12 py-3 border border-gray-400 rounded-md 
                                        focus:border-blue-500 focus:ring-2 focus:ring-blue-100 
                                        hover:border-gray-500 transition-colors" 
                                placeholder="Enter password" required>
                            <button type="button" onclick="togglePasswordVisibility('password', 'password-eye')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700">
                                <i id="password-eye" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div class="space-y-2">
                        <label class="block text-sm font-semibold text-gray-700">
                            <i class="fas fa-lock text-blue-600 mr-2"></i>
                            Confirm Password
                        </label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-key text-gray-500"></i>
                            </div>
                            <input type="password" name="password_confirmation" id="confirm-password"
                                class="w-full pl-10 pr-12 py-3 border border-gray-400 rounded-md 
                                        focus:border-blue-500 focus:ring-2 focus:ring-blue-100 
                                        hover:border-gray-500 transition-colors" 
                                placeholder="Confirm password" required>
                            <button type="button" onclick="togglePasswordVisibility('confirm-password', 'confirm-password-eye')"
                                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-500 hover:text-gray-700">
                                <i id="confirm-password-eye" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="pt-6 border-t border-gray-200 flex flex-row justify-end items-center gap-4">
                    <button type="reset" class="inline-flex items-center justify-center gap-2 px-3 py-3
                            bg-gray-200 text-gray-700 font-semibold rounded-md
                            hover:bg-gray-300 transition-colors">
                        <i class="fas fa-rotate-left"></i>
                        Reset
                    </button>
                    <button type="submit" class="inline-flex items-center justify-center gap-2 px-8 py-3
                            bg-green-600 text-white font-semibold rounded-md
                            hover:bg-green-700 transition-colors">
                        <i class="fas fa-user-plus"></i>
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        const reportDateInput = document.querySelector('input[name="report_date"]');
        const durationInput = document.querySelector('select[name="intern_duration"]');
        const endDateInput = document.getElementById('end_date');

        function calculateEndDate() {
            if (!reportDateInput.value || !durationInput.value) return;

            const reportDate = new Date(reportDateInput.value);
            const durationMonths = parseInt(durationInput.value);

            // 1 month = 4 weeks
            const totalDays = durationMonths * 4 * 7;

            const endDate = new Date(reportDate);
            endDate.setDate(endDate.getDate() + totalDays);

            endDateInput.value = endDate.toISOString().split('T')[0];
        }

        reportDateInput.addEventListener('change', calculateEndDate);
        durationInput.addEventListener('change', calculateEndDate);

        // Password visibility toggle function
        function togglePasswordVisibility(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const eyeIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }
    </script>
    @endpush
</x-app-layout>