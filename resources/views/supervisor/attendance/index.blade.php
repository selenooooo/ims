<form id="deleteForm" method="POST" style="display:none;">
    @csrf
    @method('DELETE')
</form>

<x-app-layout>
    <div class="max-w-full mx-auto px-4 space-y-6">

        <h2 class="text-2xl font-semibold text-gray-800">
            Intern Attendance Records
        </h2>

        <!-- Calendar -->
        <div class="bg-white shadow rounded-lg p-4 mt-6 max-w-full">
            <div class="flex items-center space-x-2 mb-2">
                <label for="calendarYear" class="text-sm font-medium">Year:</label>
                <input type="number" id="calendarYear" value="{{ now()->year }}" min="2000" max="2100"
                    class="border rounded px-2 py-1 w-24 text-sm">
            </div>

            <!-- Calendar container fills the card completely -->
            <div id="attendanceCalendar" class="w-full h-[600px] rounded-lg overflow-hidden" 
                style="background-color: #f9fafb;"></div>
        </div>

        <!-- Header + Controls -->
        <div class="flex justify-between items-center">

            <!-- Filters -->
            <form method="GET" class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-2 md:space-y-0 w-full md:w-auto">
                <input type="hidden" name="date" id="dateInput" value="{{ $date ?? '' }}">

                <!-- Intern Filter -->
                <select name="intern_id" class="border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="">All Active Interns</option>
                    @foreach($interns as $intern)
                        <option value="{{ $intern->id }}" {{ request('intern_id') == $intern->id ? 'selected' : '' }}>
                            {{ $intern->name }}
                        </option>
                    @endforeach
                </select>

                <!-- Year Filter (last 3 years) -->
                <!-- <select name="year" class="border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    @php $currentYear = now()->year; @endphp
                    @for($y = $currentYear; $y >= $currentYear - 10; $y--)
                        <option value="{{ $y }}" {{ request('year', $currentYear) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select> -->

                <!-- Status Filter -->
                <select name="status" class="border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none">
                    <option value="">All Status</option>
                    <option value="present" {{ request('status') === 'present' ? 'selected' : '' }}>Present</option>
                    <option value="late" {{ request('status') === 'late' ? 'selected' : '' }}>Late</option>
                    <option value="half day" {{ request('status') === 'half day' ? 'selected' : '' }}>Half Day</option>
                    <option value="on leave" {{ request('status') === 'on leave' ? 'selected' : '' }}>On Leave</option>
                </select>

                <!-- Date Range Picker -->
                <input type="text" autocomplete="off" name="date_range" id="dateRangePicker"
                       class="border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-2 focus:ring-blue-400 focus:outline-none"
                       placeholder="Select date" value="{{ request('date_range') }}">

                <!-- Buttons -->
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition">Filter</button>
                    <a href="{{ url()->current() }}" class="flex items-center bg-gray-200 hover:bg-gray-300 text-gray-800 px-3 py-2 rounded-md text-sm font-medium transition">
                        <i class="fas fa-arrows-rotate mr-1"></i> Reset
                    </a>
                    <a href="{{ url()->current() }}?show_all=1" class="flex items-center bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium transition">
                        <i class="fas fa-list mr-1"></i> Show All Attendance
                    </a>
                </div>
            </form>

            <!-- Rows per page -->
            <div>
                <form method="GET">
                    <input type="hidden" name="intern_id" value="{{ request('intern_id') }}">
                    <select name="per_page" onchange="this.form.submit()" class="border rounded px-3 py-2 text-sm">
                        @foreach([10,20,30,40] as $size)
                            <option value="{{ $size }}" {{ (int) request('per_page', 20) === $size ? 'selected' : '' }}>{{ $size }} rows</option>
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
                                <input type="time" name="check_in" id="checkin_input_{{ $attendance->id }}" value="{{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '' }}" class="border rounded px-2 py-1 w-24 text-sm hidden">
                            </td>
                            <td class="px-4 py-3">
                                <span id="checkout_display_{{ $attendance->id }}">{{ $attendance->check_out ?? '-' }}</span>
                                <input type="time" name="check_out" id="checkout_input_{{ $attendance->id }}" value="{{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '' }}" class="border rounded px-2 py-1 w-24 text-sm hidden">
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
                                    <button type="button" id="edit_btn_{{ $attendance->id }}" onclick="enableRowEdit({{ $attendance->id }})" class="text-blue-600 hover:text-blue-800 px-2 py-1 rounded text-xs"><i class="fas fa-edit"></i> Edit</button>

                                    <div id="action_buttons_{{ $attendance->id }}" class="hidden flex gap-2 justify-center mt-1">
                                        <button type="submit" class="bg-green-600 text-white px-2 py-1 rounded text-xs hover:bg-green-700">Save</button>
                                        <button type="button" onclick="deleteAttendance({{ $attendance->id }})" class="bg-red-600 text-white px-2 py-1 rounded text-xs hover:bg-red-700 ml-1"><i class="fas fa-trash"></i> Delete</button>
                                        <button type="button" class="bg-gray-300 px-2 py-1 rounded text-xs hover:bg-gray-400" onclick="cancelRowEdit({{ $attendance->id }})">Cancel</button>
                                    </div>
                                @else
                                    <span class="text-gray-400 cursor-not-allowed" title="Leave day"><i class="fas fa-edit"></i></span>
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

    <!-- JS -->
    <script>
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

        function deleteAttendance(id) {
            if (!confirm('Are you sure you want to delete this attendance record?')) return;
            const form = document.getElementById('deleteForm');
            form.action = `/supervisor/attendance/${id}`; 
            form.submit();
        }

        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('attendanceCalendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek'
                },
                events: @json($calendarEvents),
                eventDisplay: 'block',
                height: 400,
                dayMaxEventRows: 3,
                eventContent: function(arg) {
                    return { html: `<div class="text-xs font-medium">${arg.event.title}</div>` }
                },
                dateClick: function(info) {
                    const dateInput = document.getElementById('dateInput');
                    dateInput.value = info.dateStr; 
                    dateInput.closest('form').submit();
                },
                dayCellClassNames: function(arg) {
                    return ['cursor-pointer', 'hover:bg-gray-100', 'transition'];
                }
            });

            calendar.render();

            // Update calendar year
            var yearInput = document.getElementById('calendarYear');
            yearInput.addEventListener('change', function() {
                const newYear = parseInt(this.value);
                const currentDate = calendar.getDate();
                const newDate = new Date(newYear, currentDate.getMonth(), 1);
                calendar.gotoDate(newDate);
            });
        });
    </script>

</x-app-layout>
