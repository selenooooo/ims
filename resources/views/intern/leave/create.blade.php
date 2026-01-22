<x-app-layout>
    <div class="max-w-md mx-auto space-y-6">

        <h1 class="text-2xl font-bold text-gray-900">Request Leave</h1>

        <form action="{{ route('intern.leave.store') }}" method="POST" class="bg-white shadow p-6 rounded-xl space-y-4">

            @if(session('warning'))
            <div 
                x-data="{ show: true }" 
                x-show="show" 
                x-transition 
                x-init="setTimeout(() => show = false, 5000)" 
                class="fixed top-5 inset-x-0 flex justify-center z-50">
                <div class="bg-red-500 text-white top-5 px-6 py-4 rounded shadow-lg flex items-center space-x-3">
                    <span>{{ session('warning') }}</span>
                    <button 
                        type="button" 
                        @click="show = false" 
                        class="ml-auto text-white font-bold px-2 py-1 rounded hover:bg-red-600">
                        &times;
                    </button>
                </div>
            </div>
            @endif
            
            @csrf
            <div>
                <label class="block text-gray-700 font-medium">Date</label>
                <input type="text" name="leave_date" id="leave_date" class="w-full border border-gray-300 rounded-md px-3 py-2" placeholder="Pick a Date" required>
            </div>

            <div>
                <label class="block text-gray-700 font-medium">Leave Type</label>
                <select name="leave_type_id" class="w-full border border-gray-300 rounded-md px-3 py-2
                    hover:border-gray-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors duration-200" required>
                    
                    @foreach($leaveTypes as $type)
                        @if($type->code !== 'IOD') <!-- exclude Intern Off Day -->
                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                        @endif
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-medium">Half Day</label>
                <select name="half_day" class="w-full border border-gray-300 rounded-md px-3 py-2 hover:border-gray-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors duration-200" required>
                    <option value="full">Full Day</option>
                    <option value="am">Morning</option>
                    <option value="pm">Afternoon</option>
                </select>
            </div>

            <div>
                <label class="block text-gray-700 font-medium">Reason</label>
                <textarea name="reason" class="w-full border border-gray-300 rounded-md px-3 py-2 hover:border-gray-500 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 transition-colors duration-200"rows="3"></textarea>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Submit
                </button>
            </div>
        </form>

    </div>
</x-app-layout>

<script>
    flatpickr("#leave_date", {
        dateFormat: "Y-m-d",
        disable: [
            function(date) {
                return (date.getDay() === 0 || date.getDay() === 6);
            }
        ]
    });
</script>