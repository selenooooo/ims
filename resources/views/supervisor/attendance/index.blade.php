<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<x-app-layout>
    <div class="max-w-full mx-auto px-4 space-y-6">

        <h2 class="text-2xl font-semibold text-gray-800">
            Intern Attendance Records
        </h2>

        <!-- Header + Controls -->
        <div class="flex justify-between items-center">
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

            <!-- Filters -->
            <form method="GET" class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-2 md:space-y-0 w-full md:w-auto">

            <select name="intern_id"
                class="border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                <option value="">All Active Interns</option>
                @foreach($interns as $intern)
                    <option value="{{ $intern->id }}" {{ request('intern_id') == $intern->id ? 'selected' : '' }}>
                        {{ $intern->name }}
                    </option>
                @endforeach
            </select>

            <!-- Year Filter -->
            <select name="year"
                    class="border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                @php
                    $currentYear = now()->year;
                @endphp
                @for($y = $currentYear; $y >= $currentYear - 5; $y--)
                    <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endfor
            </select>

            <!-- Month Filter -->
            <select name="month" 
                    class="border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                @php
                    $months = [
                        1=>'January', 2=>'February', 3=>'March', 4=>'April', 5=>'May', 6=>'June',
                        7=>'July', 8=>'August', 9=>'September', 10=>'October', 11=>'November', 12=>'December'
                    ];
                    $currentMonth = now()->month;
                @endphp
                @foreach($months as $num => $name)
                    <option value="{{ $num }}" {{ request('month', $currentMonth) == $num ? 'selected' : '' }}>
                        {{ $name }}
                    </option>
                @endforeach
            </select>

                <!-- Status Filter -->
                <select name="status" class="border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="">All Status</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                    <option value="half day" {{ request('status') === 'half day' ? 'selected' : '' }}>Half Day</option>
                    <option value="on leave" {{ request('status') === 'on leave' ? 'selected' : '' }}>On Leave</option>
                </select>

                <!-- Date Range Picker -->
                <input type="text" autocomplete="off"  name="date_range" id="dateRangePicker"
                    class="border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none" placeholder="Select date" value="{{ request('date_range') }}">


                <!-- Buttons -->
                <div class="flex space-x-2">
                    <button type="submit"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        Filter
                    </button>

                    <a href="{{ url()->current() }}"
                    class="flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-md text-sm font-medium transition">
                        <i class="fas fa-arrows-rotate mr-1"></i> Reset
                    </a>

                    <a href="{{ url()->current() }}?show_all=1"
                    class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        <i class="fas fa-list mr-1"></i> Show All Attendance
                    </a>
                </div>
            </form>

            <!-- Rows per page -->
            <div>
                <form method="GET">
                    <input type="hidden" name="intern_id" value="{{ request('intern_id') }}">
                    <select name="per_page" onchange="this.form.submit()"
                            class="border rounded px-3 py-2 text-sm">
                        @foreach([10,20,30,40] as $size)
                            <option value="{{ $size }}"
                                {{ (int) request('per_page', 20) === $size ? 'selected' : '' }}>
                                {{ $size }} rows
                            </option>
                        @endforeach
                    </select>
                </form>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-white shadow rounded-lg overflow-hidden mt-4">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Intern</th>
                        <th class="px-4 py-3">Date</th>
                        <th class="px-4 py-3">Check In</th>
                        <th class="px-4 py-3">Check Out</th>
                        <th class="px-4 py-3">Total Hours</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($attendances as $attendance)
                    <tr class="hover:bg-gray-50 text-center">
                        <td class="px-4 py-3 text-left">{{ $attendance->user->name }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}</td>

                        <!-- Check In / Check Out Form -->
                        <form method="POST" action="{{ route('supervisor.attendance.update', $attendance->id) }}">
                            @csrf
                            @method('PATCH')
                            <td class="px-4 py-3">
                                <span id="checkin_display_{{ $attendance->id }}">{{ $attendance->check_in ?? '-' }}</span>
                                <input type="time" name="check_in" id="checkin_input_{{ $attendance->id }}"
                                    value="{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '' }}"
                                    class="border rounded px-2 py-1 w-24 text-sm hidden">
                            </td>

                            <td class="px-4 py-3">
                                <span id="checkout_display_{{ $attendance->id }}">{{ $attendance->check_out ?? '-' }}</span>
                                <input type="time" name="check_out" id="checkout_input_{{ $attendance->id }}"
                                    value="{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '' }}"
                                    class="border rounded px-2 py-1 w-24 text-sm hidden">
                            </td>

                            <td class="px-4 py-3">{{ $attendance->total_hours ?? '-' }}</td>
                            <td class="px-4 py-3">
                                <span class="px-3 py-1 rounded-full text-xs font-medium
                                    @if($attendance->status === 'present') bg-green-100 text-green-700
                                    @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-700
                                    @else bg-red-100 text-red-700 @endif">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>

                            <td class="px-4 py-3">
                                @if(!$attendance->leave_id)
                                    <button type="button" id="edit_btn_{{ $attendance->id }}"
                                            onclick="enableRowEdit({{ $attendance->id }})"
                                            class="text-blue-600 hover:text-blue-800 px-2 py-1 rounded text-xs">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <div id="action_buttons_{{ $attendance->id }}" class="hidden flex gap-2 justify-center mt-1">
                                        <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">
                                            Save
                                        </button>
                                        <button type="button" onclick="deleteAttendance({{ $attendance->id }})" class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700 ml-1">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                        <button type="button" class="bg-gray-300 px-2 py-1 rounded text-xs hover:bg-gray-400"
                                                onclick="cancelRowEdit({{ $attendance->id }})">
                                            Cancel
                                        </button>
                                    </div>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Leave day">
                                        <i class="fas fa-edit"></i>
                                    </span>
                                @endif
                            </td>
                        </form>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-6 text-gray-500">No attendance records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div>{{ $attendances->links() }}</div>
    </div>

    <!-- Search Script -->
    <script>
        const interns = @json($interns);
        const searchInput = document.getElementById('internSearch');
        const resultsBox = document.getElementById('searchResults');

        searchInput.addEventListener('input', function () {
            const value = this.value.toLowerCase();
            resultsBox.innerHTML = '';

            if (!value) {
                resultsBox.classList.add('hidden');
                return;
            }

            const matches = interns.filter(i =>
                i.name.toLowerCase().includes(value)
            );

            if (!matches.length) {
                resultsBox.classList.add('hidden');
                return;
            }

            matches.forEach(intern => {
                const div = document.createElement('div');
                div.textContent = intern.name;
                div.className = 'px-4 py-2 hover:bg-gray-100 cursor-pointer';
                div.onclick = () => {
                    const params = new URLSearchParams({
                        intern_id: intern.id,
                        per_page: '{{ request('per_page', 20) }}'
                    });

                    window.location.href =
                        `{{ route('supervisor.attendance.index') }}?${params.toString()}`;
                };

                resultsBox.appendChild(div);
            });

            resultsBox.classList.remove('hidden');
        });

        function enableRowEdit(id) {
            document.getElementById(`checkin_input_${id}`).classList.remove('hidden');
            document.getElementById(`checkout_input_${id}`).classList.remove('hidden');
            document.getElementById(`checkin_display_${id}`).classList.add('hidden');
            document.getElementById(`checkout_display_${id}`).classList.add('hidden');
            document.getElementById(`edit_btn_${id}`).classList.add('hidden');
            document.getElementById(`action_buttons_${id}`).classList.remove('hidden');
        }

        function cancelRowEdit(id) {
            document.getElementById(`checkin_input_${id}`).classList.add('hidden');
            document.getElementById(`checkout_input_${id}`).classList.add('hidden');
            document.getElementById(`checkin_display_${id}`).classList.remove('hidden');
            document.getElementById(`checkout_display_${id}`).classList.remove('hidden');
            document.getElementById(`edit_btn_${id}`).classList.remove('hidden');
            document.getElementById(`action_buttons_${id}`).classList.add('hidden');
        }

        function prepareForm(id) {
            // Copy input values to hidden form fields
            document.getElementById(`form_checkin_${id}`).value = document.getElementById(`checkin_input_${id}`).value;
            document.getElementById(`form_checkout_${id}`).value = document.getElementById(`checkout_input_${id}`).value;
        }

        function deleteAttendance(id) {
            if (!confirm('Are you sure you want to delete this attendance record?')) {
                return;
            }

            const form = document.getElementById('deleteForm');
            form.action = `/supervisor/attendance/${id}`; 
            form.submit();
        }
    </script>
</x-app-layout>
