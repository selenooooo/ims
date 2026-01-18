<x-app-layout>
    <div class="max-w-full mx-auto px-4 space-y-6">

        <!-- Page Header -->
        <div class="flex items-center justify-between border-b border-gray-200 pb-3">
            <h1 class="text-2xl text-gray-900 mb-4 md:mb-0 uppercase">
                Attendance Record
            </h1>            
        </div>

        <!-- Filters -->
            <form method="GET" class="flex flex-col md:flex-row md:items-center md:space-x-3 space-y-2 md:space-y-0 w-full md:w-auto">

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
                    <option value="half-day" {{ request('status') === 'half-day' ? 'selected' : '' }}>Half Day</option>
                    <option value="on-leave" {{ request('status') === 'on-leave' ? 'selected' : '' }}>On Leave</option>
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

        <!-- Full-Width Table -->
        <div class="overflow-x-auto w-full">
            <table class="min-w-full border border-gray-200 divide-y divide-gray-200">
                <thead class="bg-gray-50 sticky top-0 z-10">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                            Date
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                            Check In
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                            Check Out
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                            Total Hours
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider border-b">
                            Status
                        </th>
                    </tr>
                </thead>

                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($attendances as $attendance)
                        <tr class="hover:bg-gray-50 transition duration-150">
                            <td class="px-6 py-3 text-black-800">
                                {{ \Carbon\Carbon::parse($attendance->attendance_date)->format('d M Y') }}
                            </td>
                            <td class="px-6 py-3 text-gray-700">
                                {{ $attendance->check_in ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-gray-700">
                                {{ $attendance->check_out ?? '-' }}
                            </td>
                            <td class="px-6 py-3 text-gray-700">
                                {{ $attendance->total_hours ?? '-' }}
                            </td>
                            <td class="px-6 py-3">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                    @if($attendance->status === 'present') bg-green-100 text-green-700
                                    @elseif($attendance->status === 'late') bg-yellow-100 text-yellow-700
                                    @elseif($attendance->status === 'half-day') bg-indigo-100 text-indigo-700
                                    @elseif($attendance->status === 'on-leave') bg-purple-100 text-purple-700
                                    @else bg-red-100 text-red-700
                                    @endif">
                                    {{ ucfirst($attendance->status) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-6 text-center text-gray-400">
                                No attendance records found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

<script>
    flatpickr("#dateRangePicker", {
        mode: "range",        // single date or range
        dateFormat: "Y-m-d",  // format compatible with your controller
        allowInput: true,     // allows typing manually
        defaultDate: "{{ request('date_range') }}",
    });
</script>
