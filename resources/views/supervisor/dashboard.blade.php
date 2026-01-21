<x-app-layout>
    <div class="max-w-full mx-auto px-4 space-y-6">

        <!-- Header Row with Welcome, Date & Time -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    Welcome, {{ auth()->user()->name }}
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Supervisor Dashboard
                </p>
            </div>
            
            <div class="flex flex-col sm:items-end gap-2">
                <!-- Date -->
                <div class="text-right">
                    <p id="date" class="text-lg font-medium text-gray-800"></p>
                </div>
                
                <!-- Live Clock -->
                <div class="text-right">
                    <p id="clock" class="text-2xl font-bold text-gray-900 tracking-tight"></p>
                </div>
            </div>
        </div>

        <!-- Today's Attendance -->
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Today's Attendance</h3>
                <div class="text-sm text-gray-500">
                    {{ $todayAttendances->count() }} intern(s) recorded
                </div>
            </div>

            @if($todayAttendances->count() > 0)
                <div class="overflow-x-auto rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Intern Name
                                </th>
                                <!-- <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Email
                                </th> -->
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check In
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Check Out
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Hours
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($todayAttendances as $attendance)
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-m font-medium text-gray-900">
                                            {{ $attendance->user->name }}
                                        </div>
                                    </td>
                                    <!-- <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-600">
                                            {{ $attendance->user->email }}
                                        </div>
                                    </td> -->
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('h:i A') : '--:--' }}
                                        </div>
                                        <!-- @if($attendance->check_in)
                                            <div class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($attendance->check_in)->format('g:i A') }}
                                            </div>
                                        @endif -->
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900 font-medium">
                                            {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('h:i A') : '--:--' }}
                                        </div>
                                        <!-- @if($attendance->check_out)
                                            <div class="text-xs text-gray-500">
                                                {{ \Carbon\Carbon::parse($attendance->check_out)->format('g:i A') }}
                                            </div>
                                        @endif -->
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <!-- @php
                                            $statusColors = [
                                                'present' => 'bg-green-100 text-green-800 border-green-200',
                                                'late' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                                'half-day' => 'bg-blue-100 text-blue-800 border-blue-200',
                                                'on-leave' => 'bg-purple-100 text-purple-800 border-purple-200',
                                                'absent' => 'bg-red-100 text-red-800 border-red-200',
                                                'pending' => 'bg-gray-100 text-gray-800 border-gray-200',
                                            ];
                                            $status = $attendance->status ?? 'pending';
                                            $colorClass = $statusColors[$status] ?? $statusColors['pending'];
                                        @endphp
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $colorClass }}">
                                            {{ ucfirst(str_replace('-', ' ', $status)) }}
                                        </span> -->
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border {{ $colorClass }}">
                                            {{ $attendance->total_hours ?? '-' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No attendance records for today</h3>
                    <p class="mt-1 text-sm text-gray-500">Check back later for today's attendance records.</p>
                </div>
            @endif

            <div class="mt-6 flex justify-end">
                <a href="{{ route('supervisor.attendance.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-900 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    View Full Attendance
                </a>
            </div>
        </div>

        <!-- TODAY LEAVE TABLE -->
        <div class="mt-10 bg-white border border-gray-200 rounded-md shadow-sm">
            <div class="px-6 py-4 border-b flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-700 uppercase tracking-wide">
                    <span class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold
                        rounded-full bg-red-100 text-red-700">
                        <i class="fas fa-calendar-times"></i>
                    </span>
                    Interns On Leave Today
                </h3>
            </div>

            @if($todayLeaves->count())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">
                                    Name
                                </th>
                                <!-- <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">
                                    Date
                                </th> -->
                                <th class="px-6 py-3 text-left font-medium text-gray-500 uppercase">
                                    Leave Type
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($todayLeaves as $leave)
                                <tr>
                                    <td class="px-6 py-3 text-gray-800 font-medium">
                                        {{ $leave->user->name ?? '-' }}
                                    </td>
                                    <!-- <td class="px-6 py-3 text-gray-600">
                                        {{ \Carbon\Carbon::parse($leave->attendance_date)->format('d M Y') }}
                                    </td> -->
                                    <td class="px-6 py-3 text-gray-600">
                                        {{ $leave->leave->leaveType->name ?? '—' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-4 text-sm text-gray-500">
                    No interns on leave today.
                </div>
            @endif
        </div>

    </div>

    <!-- Real-time Clock Script -->
    <script>
        function updateClock() {
            const now = new Date();

            document.getElementById('clock').innerText =
                now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', second: '2-digit' });

            document.getElementById('date').innerText =
                now.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
        }

        setInterval(updateClock, 1000);
        updateClock();
    </script>
</x-app-layout>