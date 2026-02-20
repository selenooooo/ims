<x-app-layout>
    <div class="max-w-md mx-auto space-y-6">

        <h1 class="text-2xl font-bold text-gray-900">Add Leave for Intern</h1>

        <form action="{{ route('supervisor.leave.store') }}" method="POST" onsubmit="return handleSubmit(event)" novalidate class="bg-white shadow p-6 rounded-xl space-y-4">
            @csrf

            <!-- Date -->
            <div>
                <label class="block text-gray-700 font-medium">Date</label>
                <input type="text" name="leave_date" id="leave_date" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Pick a Date" required>
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
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Add Leave
                </button>
            </div>

        </form>
    </div>
</x-app-layout>

<script>
    function handleSubmit(event) {
        event.preventDefault(); // ALWAYS stop default first

        const form = event.target;
        const dateInput = document.getElementById('leave_date');
        const internSelect = form.querySelector('select[name="user_id"]');
        const leaveTypeSelect = form.querySelector('select[name="leave_type_id"]');
        const halfDaySelect = form.querySelector('select[name="half_day"]');
        const button = form.querySelector('button[type="submit"]');

        //Check Date
        if (!dateInput.value) {
            alert("Please choose a date.");
            dateInput.focus();
            return false;
        }

        // Check Intern
        if (!internSelect.value) {
            alert("Please select an intern.");
            internSelect.focus();
            return false;
        }

        // Check Leave Type
        if (!leaveTypeSelect.value) {
            alert("Please select leave type.");
            leaveTypeSelect.focus();
            return false;
        }

        // Check Half Day
        if (!halfDaySelect.value) {
            alert("Please select half day option.");
            halfDaySelect.focus();
            return false;
        }

        // If everything valid → disable button & submit
        button.disabled = true;
        button.innerText = "Submitting...";
        button.classList.remove('bg-blue-600', 'hover:bg-blue-700');
        button.classList.add('bg-gray-400', 'cursor-not-allowed');

        form.submit();
    }
    
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr("#leave_date", {
            dateFormat: "Y-m-d",
            disable: [
                function(date) {
                    return (date.getDay() === 0 || date.getDay() === 6);
                }
            ]
        });
    });
</script>