<x-app-layout>
    <div class="max-w-md mx-auto space-y-6">

        <h1 class="text-2xl font-bold text-gray-900">Add Leave for Intern</h1>

        <form action="{{ route('supervisor.leave.store') }}" method="POST" class="bg-white shadow p-6 rounded-xl space-y-4">
            @csrf

            <!-- Date -->
            <div>
                <label class="block text-gray-700 font-medium">Date</label>
                <input type="date" name="leave_date" class="w-full border border-gray-300 rounded-md px-3 py-2 
                    hover:border-gray-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors duration-200" required>
            </div>

            <!-- Intern Dropdown -->
            <div>
                <label class="block text-gray-700 font-medium">Intern</label>
                <select name="user_id" class="w-full border border-gray-300 rounded-md px-3 py-2 
                    hover:border-gray-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors duration-200" required>
                    <option value="">-- Select Intern --</option>
                    <option value="all">ALL INTERN</option>
                    @foreach($interns as $intern)
                        <option value="{{ $intern->id }}">{{ $intern->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Leave Type -->
            <div>
                <label class="block text-gray-700 font-medium">Leave Type</label>
                <select name="leave_type_id" class="w-full border border-gray-300 rounded-md px-3 py-2
                    hover:border-gray-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors duration-200" required>
                    @foreach($leaveTypes as $type)
                        <option value="{{ $type->id }}">{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Half Day -->
            <div>
                <label class="block text-gray-700 font-medium">Half Day</label>
                <select name="half_day" class="w-full border border-gray-300 rounded-md px-3 py-2 
                    hover:border-gray-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors duration-200" required>
                    <option value="full">Full Day</option>
                    <option value="am">Morning</option>
                    <option value="pm">Afternoon</option>
                </select>
            </div>

            <!-- Reason -->
            <div>
                <label class="block text-gray-700 font-medium">Reason</label>
                <textarea name="reason" class="w-full border border-gray-300 rounded-md px-3 py-2 
                    hover:border-gray-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors duration-200" rows="3"></textarea>
            </div>

            <!-- Submit -->
            <div class="flex justify-end">
                <button type="submit" onclick="disableSubmit(this)" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add Leave
                </button>
            </div>

        </form>
    </div>
</x-app-layout>

<script>
    function disableSubmit(button) {
        button.disabled = true;
        button.innerText = "Submitting...";
        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        button.classList.add('bg-gray-400', 'cursor-not-allowed');
        button.form.submit();
    }
</script>

